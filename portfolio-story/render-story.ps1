$ErrorActionPreference = 'Stop'

Add-Type -AssemblyName System.Drawing

$OutputDirectory = Split-Path -Parent $MyInvocation.MyCommand.Path
$BackgroundPath = Join-Path $OutputDirectory 'background-ai.png'
$LogoPath = Join-Path $OutputDirectory 'logo-newman.png'

if (-not (Test-Path -LiteralPath $BackgroundPath)) {
    throw "Missing background image: $BackgroundPath"
}

if (-not (Test-Path -LiteralPath $LogoPath)) {
    throw "Missing logo image: $LogoPath"
}

$script:Width = 1080
$script:Height = 1920
$script:Gold = [System.Drawing.ColorTranslator]::FromHtml('#D5A85A')
$script:Sand = [System.Drawing.ColorTranslator]::FromHtml('#F6EFE5')
$script:White = [System.Drawing.Color]::FromArgb(245, 255, 255, 255)
$script:Muted = [System.Drawing.Color]::FromArgb(190, 224, 231, 238)
$script:Subtle = [System.Drawing.Color]::FromArgb(130, 224, 231, 238)
$script:Navy = [System.Drawing.ColorTranslator]::FromHtml('#071E33')
$script:Background = [System.Drawing.Bitmap]::FromFile($BackgroundPath)
$script:Logo = [System.Drawing.Bitmap]::FromFile($LogoPath)

function New-StoryFont {
    param(
        [float] $Size,
        [System.Drawing.FontStyle] $Style = [System.Drawing.FontStyle]::Regular
    )

    return [System.Drawing.Font]::new('Arial', $Size, $Style, [System.Drawing.GraphicsUnit]::Pixel)
}

function New-TextFormat {
    param(
        [System.Drawing.StringAlignment] $Alignment = [System.Drawing.StringAlignment]::Near,
        [System.Drawing.StringAlignment] $LineAlignment = [System.Drawing.StringAlignment]::Near
    )

    $format = [System.Drawing.StringFormat]::new()
    $format.Alignment = $Alignment
    $format.LineAlignment = $LineAlignment
    $format.Trimming = [System.Drawing.StringTrimming]::Word
    $format.FormatFlags = [System.Drawing.StringFormatFlags]::LineLimit
    return $format
}

function Draw-Text {
    param(
        [System.Drawing.Graphics] $Graphics,
        [string] $Text,
        [float] $X,
        [float] $Y,
        [float] $Width,
        [float] $Height,
        [float] $Size,
        [System.Drawing.Color] $Color,
        [System.Drawing.FontStyle] $Style = [System.Drawing.FontStyle]::Regular,
        [System.Drawing.StringAlignment] $Alignment = [System.Drawing.StringAlignment]::Near,
        [System.Drawing.StringAlignment] $LineAlignment = [System.Drawing.StringAlignment]::Near
    )

    $font = New-StoryFont -Size $Size -Style $Style
    $brush = [System.Drawing.SolidBrush]::new($Color)
    $format = New-TextFormat -Alignment $Alignment -LineAlignment $LineAlignment
    try {
        $Graphics.DrawString($Text, $font, $brush, [System.Drawing.RectangleF]::new($X, $Y, $Width, $Height), $format)
    } finally {
        $format.Dispose()
        $brush.Dispose()
        $font.Dispose()
    }
}

function Draw-CanvasBackground {
    param([System.Drawing.Graphics] $Graphics)

    $Graphics.DrawImage($script:Background, [System.Drawing.Rectangle]::new(0, 0, $script:Width, $script:Height))
    $overlay = [System.Drawing.SolidBrush]::new([System.Drawing.Color]::FromArgb(52, 0, 15, 29))
    try {
        $Graphics.FillRectangle($overlay, 0, 0, $script:Width, $script:Height)
    } finally {
        $overlay.Dispose()
    }
}

function Draw-Header {
    param(
        [System.Drawing.Graphics] $Graphics,
        [string] $Label
    )

    $Graphics.DrawImage($script:Logo, [System.Drawing.Rectangle]::new(78, 75, 58, 58))
    Draw-Text $Graphics 'N E W M A N' 155 76 330 32 23 $script:White ([System.Drawing.FontStyle]::Bold)
    Draw-Text $Graphics 'T O U R  B A L I' 155 113 300 24 14 $script:Muted ([System.Drawing.FontStyle]::Bold)
    Draw-Text $Graphics $Label 610 88 390 30 16 $script:Gold ([System.Drawing.FontStyle]::Bold) ([System.Drawing.StringAlignment]::Far)
}

function Draw-Eyebrow {
    param(
        [System.Drawing.Graphics] $Graphics,
        [string] $Text,
        [float] $Y = 245
    )

    Draw-Text $Graphics $Text.ToUpperInvariant() 78 $Y 920 34 17 $script:Gold ([System.Drawing.FontStyle]::Bold)
}

function Draw-MixedTitle {
    param(
        [System.Drawing.Graphics] $Graphics,
        [string] $First,
        [string] $Gold,
        [float] $Y,
        [float] $FirstHeight = 90,
        [float] $GoldHeight = 90,
        [float] $Size = 68
    )

    Draw-Text $Graphics $First 78 $Y 930 $FirstHeight $Size $script:White ([System.Drawing.FontStyle]::Bold)
    Draw-Text $Graphics $Gold 78 ($Y + $FirstHeight - 2) 930 $GoldHeight $Size $script:Gold ([System.Drawing.FontStyle]::Bold)
}

function Draw-Card {
    param(
        [System.Drawing.Graphics] $Graphics,
        [float] $X,
        [float] $Y,
        [float] $Width,
        [float] $Height,
        [string] $Label,
        [string] $Title,
        [string[]] $Items
    )

    $fill = [System.Drawing.SolidBrush]::new([System.Drawing.Color]::FromArgb(155, 2, 20, 34))
    $border = [System.Drawing.Pen]::new([System.Drawing.Color]::FromArgb(75, 213, 168, 90), 1.5)
    try {
        $Graphics.FillRectangle($fill, $X, $Y, $Width, $Height)
        $Graphics.DrawRectangle($border, $X, $Y, $Width, $Height)
    } finally {
        $fill.Dispose()
        $border.Dispose()
    }

    Draw-Text $Graphics $Label.ToUpperInvariant() ($X + 28) ($Y + 26) ($Width - 56) 28 15 $script:Gold ([System.Drawing.FontStyle]::Bold)
    $contentY = $Y + 70
    if ($Title) {
        Draw-Text $Graphics $Title ($X + 28) $contentY ($Width - 56) 48 27 $script:White ([System.Drawing.FontStyle]::Bold)
        $contentY += 57
    }

    $bulletFont = New-StoryFont -Size 21
    $bulletBrush = [System.Drawing.SolidBrush]::new($script:Muted)
    $goldBrush = [System.Drawing.SolidBrush]::new($script:Gold)
    $measureFormat = New-TextFormat
    try {
        foreach ($item in $Items) {
            $Graphics.FillEllipse($goldBrush, $X + 31, $contentY + 10, 7, 7)
            $rect = [System.Drawing.RectangleF]::new($X + 52, $contentY, $Width - 78, 62)
            $Graphics.DrawString($item, $bulletFont, $bulletBrush, $rect, $measureFormat)
            $measured = $Graphics.MeasureString($item, $bulletFont, [int]($Width - 78), $measureFormat)
            $contentY += [Math]::Max(38, [Math]::Ceiling($measured.Height) + 8)
        }
    } finally {
        $measureFormat.Dispose()
        $goldBrush.Dispose()
        $bulletBrush.Dispose()
        $bulletFont.Dispose()
    }
}

function Draw-Footer {
    param(
        [System.Drawing.Graphics] $Graphics,
        [string] $Left,
        [string] $Right,
        [int] $Active
    )

    Draw-Text $Graphics $Left 78 1770 660 34 17 $script:Muted ([System.Drawing.FontStyle]::Bold)
    Draw-Text $Graphics $Right 745 1770 255 34 17 $script:Muted ([System.Drawing.FontStyle]::Bold) ([System.Drawing.StringAlignment]::Far)

    $segmentWidth = 146
    $segmentGap = 8
    for ($index = 1; $index -le 6; $index++) {
        $color = if ($index -le $Active) { $script:Gold } else { [System.Drawing.Color]::FromArgb(80, 210, 220, 230) }
        $brush = [System.Drawing.SolidBrush]::new($color)
        try {
            $Graphics.FillRectangle($brush, 78 + (($index - 1) * ($segmentWidth + $segmentGap)), 1824, $segmentWidth, 6)
        } finally {
            $brush.Dispose()
        }
    }
}

function New-StoryCanvas {
    $bitmap = [System.Drawing.Bitmap]::new($script:Width, $script:Height, [System.Drawing.Imaging.PixelFormat]::Format32bppArgb)
    $graphics = [System.Drawing.Graphics]::FromImage($bitmap)
    $graphics.SmoothingMode = [System.Drawing.Drawing2D.SmoothingMode]::HighQuality
    $graphics.InterpolationMode = [System.Drawing.Drawing2D.InterpolationMode]::HighQualityBicubic
    $graphics.PixelOffsetMode = [System.Drawing.Drawing2D.PixelOffsetMode]::HighQuality
    $graphics.TextRenderingHint = [System.Drawing.Text.TextRenderingHint]::ClearTypeGridFit
    Draw-CanvasBackground $graphics
    return @{ Bitmap = $bitmap; Graphics = $graphics }
}

function Save-StoryCanvas {
    param(
        [hashtable] $Canvas,
        [int] $Number
    )

    $fileName = 'newman-portfolio-story-{0:D2}.png' -f $Number
    $outputPath = Join-Path $OutputDirectory $fileName
    try {
        $Canvas.Bitmap.Save($outputPath, [System.Drawing.Imaging.ImageFormat]::Png)
    } finally {
        $Canvas.Graphics.Dispose()
        $Canvas.Bitmap.Dispose()
    }

    return $outputPath
}

function Draw-SlideOne {
    $canvas = New-StoryCanvas
    $graphics = $canvas.Graphics
    Draw-Header $graphics 'PORTFOLIO SYSTEM AUDIT  ·  01/06'
    Draw-Eyebrow $graphics 'Full-stack travel platform' 258
    Draw-MixedTitle $graphics 'More than a' 'tour website.' 310 104 106 86
    Draw-Text $graphics 'A responsive Bali travel experience, booking-request engine, pricing & availability system, and operations CMS—built as one Laravel application.' 78 555 905 165 31 $script:Muted

    $metrics = @(
        @{ X = 78;  Y = 820; Value = '96';  Label = 'application routes' },
        @{ X = 555; Y = 820; Value = '22';  Label = 'controllers' },
        @{ X = 78;  Y = 1070; Value = '68'; Label = 'Blade views' },
        @{ X = 555; Y = 1070; Value = '≈44K'; Label = 'application source lines' }
    )

    foreach ($metric in $metrics) {
        $fill = [System.Drawing.SolidBrush]::new([System.Drawing.Color]::FromArgb(145, 2, 20, 34))
        $border = [System.Drawing.Pen]::new([System.Drawing.Color]::FromArgb(72, 213, 168, 90), 1.5)
        try {
            $graphics.FillRectangle($fill, $metric.X, $metric.Y, 447, 215)
            $graphics.DrawRectangle($border, $metric.X, $metric.Y, 447, 215)
        } finally {
            $fill.Dispose()
            $border.Dispose()
        }
        Draw-Text $graphics $metric.Value ($metric.X + 30) ($metric.Y + 28) 385 95 67 $script:Gold ([System.Drawing.FontStyle]::Bold)
        Draw-Text $graphics $metric.Label ($metric.X + 32) ($metric.Y + 130) 380 40 22 $script:White ([System.Drawing.FontStyle]::Bold)
    }

    Draw-Text $graphics 'A product engineering case study—not a simple company profile.' 78 1415 920 80 27 $script:White ([System.Drawing.FontStyle]::Bold)
    Draw-Footer $graphics 'Laravel 13  ·  PHP 8.3  ·  MySQL  ·  Tailwind CSS 4' 'Repository + rendered-app audit' 1
    return Save-StoryCanvas $canvas 1
}

function Draw-FourCardSlide {
    param(
        [int] $Number,
        [string] $PageLabel,
        [string] $Eyebrow,
        [string] $TitleWhite,
        [string] $TitleGold,
        [array] $Cards,
        [string] $FooterLeft,
        [string] $FooterRight,
        [string] $Note = ''
    )

    $canvas = New-StoryCanvas
    $graphics = $canvas.Graphics
    Draw-Header $graphics $PageLabel
    Draw-Eyebrow $graphics $Eyebrow 245
    Draw-MixedTitle $graphics $TitleWhite $TitleGold 295 82 88 66

    $cardY = if ($Note) { 540 } else { 505 }
    $positions = @(
        @{ X = 78;  Y = $cardY },
        @{ X = 555; Y = $cardY },
        @{ X = 78;  Y = $cardY + 385 },
        @{ X = 555; Y = $cardY + 385 }
    )

    for ($index = 0; $index -lt 4; $index++) {
        $card = $Cards[$index]
        $position = $positions[$index]
        Draw-Card $graphics $position.X $position.Y 447 355 $card.Label $card.Title $card.Items
    }

    if ($Note) {
        $noteFill = [System.Drawing.SolidBrush]::new([System.Drawing.Color]::FromArgb(160, 19, 48, 68))
        try {
            $graphics.FillRectangle($noteFill, 78, 1322, 924, 108)
        } finally {
            $noteFill.Dispose()
        }
        Draw-Text $graphics $Note 105 1345 870 65 21 $script:Sand ([System.Drawing.FontStyle]::Bold)
    }

    Draw-Footer $graphics $FooterLeft $FooterRight $Number
    return Save-StoryCanvas $canvas $Number
}

function Draw-SlideSix {
    $canvas = New-StoryCanvas
    $graphics = $canvas.Graphics
    Draw-Header $graphics 'ENGINEERING STATS  ·  06/06'
    Draw-Eyebrow $graphics 'Measured from the repository' 245
    Draw-MixedTitle $graphics 'The build,' 'by the numbers.' 295 80 90 68

    $stats = @(
        @{ Value = '13'; Label = 'Eloquent models' },
        @{ Value = '17'; Label = 'migration files' },
        @{ Value = '20'; Label = 'tables created' },
        @{ Value = '14'; Label = 'Form Requests' },
        @{ Value = '4'; Label = 'business services' },
        @{ Value = '10'; Label = 'Blade components' },
        @{ Value = '48'; Label = 'authored test methods' },
        @{ Value = '223'; Label = 'assert calls' },
        @{ Value = '51'; Label = 'image assets' }
    )

    for ($index = 0; $index -lt $stats.Count; $index++) {
        $column = $index % 3
        $row = [Math]::Floor($index / 3)
        $x = 78 + ($column * 313)
        $y = 500 + ($row * 205)
        $fill = [System.Drawing.SolidBrush]::new([System.Drawing.Color]::FromArgb(150, 2, 20, 34))
        $border = [System.Drawing.Pen]::new([System.Drawing.Color]::FromArgb(70, 213, 168, 90), 1.5)
        try {
            $graphics.FillRectangle($fill, $x, $y, 291, 181)
            $graphics.DrawRectangle($border, $x, $y, 291, 181)
        } finally {
            $fill.Dispose()
            $border.Dispose()
        }
        Draw-Text $graphics $stats[$index].Value ($x + 25) ($y + 22) 240 72 51 $script:Gold ([System.Drawing.FontStyle]::Bold)
        Draw-Text $graphics $stats[$index].Label ($x + 25) ($y + 105) 240 52 18 $script:White ([System.Drawing.FontStyle]::Bold)
    }

    $panelFill = [System.Drawing.SolidBrush]::new([System.Drawing.Color]::FromArgb(180, 22, 50, 70))
    try {
        $graphics.FillRectangle($panelFill, 78, 1145, 924, 390)
    } finally {
        $panelFill.Dispose()
    }
    Draw-Text $graphics 'CONTENT SNAPSHOT AT AUDIT TIME' 105 1180 840 34 16 $script:Gold ([System.Drawing.FontStyle]::Bold)

    $snapshot = @(
        @{ X = 105; Y = 1245; Label = 'Tour products'; Value = '3' },
        @{ X = 555; Y = 1245; Label = 'Active / draft'; Value = '1 / 2' },
        @{ X = 105; Y = 1310; Label = 'Tour options'; Value = '3' },
        @{ X = 555; Y = 1310; Label = 'Roadmap stops'; Value = '15' },
        @{ X = 105; Y = 1375; Label = 'Gallery moments'; Value = '26' },
        @{ X = 555; Y = 1375; Label = 'Package requests'; Value = '13' },
        @{ X = 105; Y = 1440; Label = 'Completed guests'; Value = '6' },
        @{ X = 555; Y = 1440; Label = 'Verified ratings'; Value = '1' }
    )

    foreach ($item in $snapshot) {
        Draw-Text $graphics $item.Label $item.X $item.Y 275 35 18 $script:Muted
        Draw-Text $graphics $item.Value ($item.X + 285) $item.Y 85 35 22 $script:White ([System.Drawing.FontStyle]::Bold) ([System.Drawing.StringAlignment]::Far)
    }
    Draw-Text $graphics 'Snapshot: 17 Aug 2026. Live database counts change as content and bookings grow.' 105 1500 845 30 14 $script:Subtle
    Draw-Footer $graphics 'Audit date  ·  17 Aug 2026' 'Newman Tour Bali' 6
    return Save-StoryCanvas $canvas 6
}

try {
    $created = [System.Collections.Generic.List[string]]::new()
    $created.Add((Draw-SlideOne))

    $created.Add((Draw-FourCardSlide -Number 2 -PageLabel 'TECH STACK  ·  02/06' -Eyebrow 'The ecosystem' -TitleWhite 'Built on a modern' -TitleGold 'Laravel stack.' -Cards @(
        @{ Label = 'Backend'; Title = 'Application core'; Items = @('PHP 8.3.30 + Laravel 13.19', 'Eloquent ORM + MySQL 8.4', 'MVC, service layer & observers', 'Blade SSR + reusable components', 'Form Requests + Notifications') },
        @{ Label = 'Frontend'; Title = 'Responsive UI'; Items = @('Tailwind CSS 4.3', 'Alpine.js 3.15', 'Swiper 14 carousels', 'AOS 2.3 motion', 'Vite 8.1 bundling') },
        @{ Label = 'Infrastructure'; Title = 'Runtime & tooling'; Items = @('Database sessions, cache & queue', 'SMTP mail notifications', 'Public/private filesystem storage', 'Composer + npm workflows', 'PHPUnit 12.5 + Pint 1.29') },
        @{ Label = 'Integrations'; Title = 'Travel ecosystem'; Items = @('Leaflet 1.9 + OpenStreetMap', 'Google Maps embed', 'WhatsApp deep links', 'Tripadvisor & social links', 'Dynamic sitemap + robots.txt') }
    ) -FooterLeft 'Server-rendered monolith  ·  not a SPA' -FooterRight 'Progressive enhancement'))

    $created.Add((Draw-FourCardSlide -Number 3 -PageLabel 'GUEST EXPERIENCE  ·  03/06' -Eyebrow 'Public-facing features' -TitleWhite 'From discovery to a' -TitleGold 'qualified request.' -Cards @(
        @{ Label = 'Discover'; Title = ''; Items = @('Responsive homepage & mobile nav', 'Hero, tour & review carousels', 'Tour catalogue + categories', 'Detail gallery + verified stats', 'Roadmap + interactive route map') },
        @{ Label = 'Plan'; Title = ''; Items = @('Date, language & guest selection', 'Live option availability', 'Start-time & capacity checks', 'Price breakdown + discounts', 'Vehicle-fit recommendation') },
        @{ Label = 'Convert'; Title = ''; Items = @('Review-before-submit flow', 'Unique booking reference', '4-step Custom Trip Builder', 'SMTP notification to admin', 'WhatsApp follow-up paths') },
        @{ Label = 'Trust & content'; Title = ''; Items = @('Verified-trip rating flow', 'EN/ID navigation switching', 'Filterable guest gallery', 'Contact + embedded map', 'Canonical URL + sitemap') }
    ) -FooterLeft 'Mobile audit: 390 × 844  ·  no horizontal overflow' -FooterRight 'Private-tour UX' -Note 'Business boundary: booking request + manual confirmation. No website payment, instant voucher, or automatic vehicle confirmation.'))

    $created.Add((Draw-FourCardSlide -Number 4 -PageLabel 'BUSINESS LOGIC  ·  04/06' -Eyebrow 'Under the interface' -TitleWhite 'Rules that protect' -TitleGold 'price, capacity & data.' -Cards @(
        @{ Label = 'Availability engine'; Title = ''; Items = @('Day-of-week operating schedules', 'Availability date windows', 'Booking cutoff by departure time', 'Whole-day / time-slot blackout', 'Option + schedule capacity limits') },
        @{ Label = 'Pricing engine'; Title = ''; Items = @('Adult, child & infant pricing', 'Free / disallowed participant rows', 'Fixed or percentage discount', 'Participant-scoped promotions', 'Highest-priority valid discount') },
        @{ Label = 'Integrity'; Title = ''; Items = @('Server-side price recheck on submit', 'Database transaction for booking', 'Immutable JSON booking snapshots', 'Unique public booking references', 'Soft deletes for operational data') },
        @{ Label = 'Trust controls'; Title = ''; Items = @('Signed guest rating URLs', '10/min rating throttling', 'One rating per completed trip', 'Row locks against duplicates', 'Session-gated success pages') }
    ) -FooterLeft 'Availability + pricing recomputed from trusted data' -FooterRight 'Service-layer architecture'))

    $created.Add((Draw-FourCardSlide -Number 5 -PageLabel 'OPERATIONS CMS  ·  05/06' -Eyebrow '77 protected admin routes' -TitleWhite 'A complete' -TitleGold 'back-office workflow.' -Cards @(
        @{ Label = 'Bookings'; Title = ''; Items = @('Search, filter & pagination', 'Full selection / price snapshots', 'Status lifecycle management', 'Protected deletion rules', 'Generate signed rating link') },
        @{ Label = 'Tour CMS'; Title = ''; Items = @('Tour products + options', 'Prices + included/excluded items', 'Schedules + blackout dates', 'Discounts + route stops', 'Main image + ordered gallery') },
        @{ Label = 'Publishing guard'; Title = ''; Items = @('Draft preview without publishing', 'Product readiness report', 'Blocking vs warning checks', 'Observer-enforced activation', 'Health counters refresh every 15s') },
        @{ Label = 'Content & insights'; Title = ''; Items = @('Custom Trip operations queue', 'Gallery moment management', 'Verified rating summaries', 'Rating search, tour filter & sort', 'Popular / featured controls') }
    ) -FooterLeft 'Session-protected operations interface' -FooterRight 'Responsive admin layout' -Note 'PENDING  →  CONTACTED  →  CONFIRMED  →  COMPLETED / CANCELLED'))

    $created.Add((Draw-SlideSix))
    $created | ForEach-Object { Get-Item -LiteralPath $_ | Select-Object FullName, Length }
} finally {
    $script:Logo.Dispose()
    $script:Background.Dispose()
}
