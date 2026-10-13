<script>
    (() => {
        const measurementId = 'G-SZTT2716D1';
        const consentKey = 'newman.analytics-consent';
        const consentLifetime = 180 * 24 * 60 * 60 * 1000;
        const disableKey = 'ga-disable-' + measurementId;
        let analyticsLoaded = false;

        const loadAnalytics = () => {
            window[disableKey] = false;
            if (analyticsLoaded) {
                window.gtag('consent', 'update', { analytics_storage: 'granted' });
                return;
            }
            analyticsLoaded = true;

            window.dataLayer = window.dataLayer || [];
            window.gtag = function () { window.dataLayer.push(arguments); };
            window.gtag('consent', 'default', {
                analytics_storage: 'granted',
                ad_storage: 'denied',
                ad_user_data: 'denied',
                ad_personalization: 'denied',
            });
            window.gtag('js', new Date());
            window.gtag('config', measurementId, {
                page_location: window.location.origin + window.location.pathname,
                page_referrer: document.referrer ? document.referrer.split('?')[0].split('#')[0] : '',
                allow_google_signals: false,
                allow_ad_personalization_signals: false,
            });

            const tag = document.createElement('script');
            tag.async = true;
            tag.src = 'https://www.googletagmanager.com/gtag/js?id=' + measurementId;
            document.head.appendChild(tag);
        };

        const stopAnalytics = () => {
            window[disableKey] = true;
            if (analyticsLoaded) {
                window.gtag('consent', 'update', { analytics_storage: 'denied' });
            }
            const host = window.location.hostname;
            const domains = ['', host, '.' + host, host.replace(/^www\./, ''), '.' + host.replace(/^www\./, '')];
            ['_ga', '_ga_' + measurementId.slice(2)].forEach((name) => {
                domains.forEach((domain) => {
                    document.cookie = name + '=; Max-Age=0; path=/' + (domain ? '; domain=' + domain : '');
                });
            });
        };

        const rememberConsent = (choice) => {
            try {
                localStorage.setItem(consentKey, JSON.stringify({ choice, savedAt: Date.now() }));
            } catch (_) {
                // The visitor's choice still applies to this page when storage is unavailable.
            }
        };

        document.addEventListener('DOMContentLoaded', () => {
            const template = document.getElementById('analytics-consent-template');
            let panel;
            const showConsent = () => {
                if (panel && panel.isConnected) return;
                panel = template.content.firstElementChild.cloneNode(true);
                panel.querySelectorAll('[data-analytics-consent]').forEach((button) => {
                    button.addEventListener('click', () => {
                        const choice = button.dataset.analyticsConsent;
                        rememberConsent(choice);
                        if (choice === 'granted') loadAnalytics();
                        else stopAnalytics();
                        panel.remove();
                    });
                });
                document.body.appendChild(panel);
            };

            const settings = document.createElement('button');
            settings.type = 'button';
            settings.textContent = @js(__('Analytics settings'));
            settings.className = 'block mx-auto px-4 py-3 text-xs underline underline-offset-4';
            settings.addEventListener('click', showConsent);
            (document.querySelector('footer') || document.body).appendChild(settings);

            let savedConsent;
            try {
                savedConsent = JSON.parse(localStorage.getItem(consentKey));
            } catch (_) {
                savedConsent = null;
            }

            const consentIsCurrent = savedConsent
                && ['granted', 'denied'].includes(savedConsent.choice)
                && Number.isFinite(savedConsent.savedAt)
                && Date.now() - savedConsent.savedAt >= 0
                && Date.now() - savedConsent.savedAt < consentLifetime;

            if (consentIsCurrent) {
                if (savedConsent.choice === 'granted') loadAnalytics();
                return;
            }

            showConsent();
        }, { once: true });
    })();
</script>

<template id="analytics-consent-template">
    <section
        class="fixed bottom-4 left-4 z-[90] w-[calc(100%-2rem)] max-w-sm rounded-xl border border-newman-navy/15 bg-white p-5 text-newman-navy shadow-xl"
        aria-labelledby="analytics-consent-title"
    >
        <h2 id="analytics-consent-title" class="text-base font-semibold">{{ __('Website analytics') }}</h2>
        <p class="mt-2 text-sm leading-6 text-gray-600">
            {{ __('Allow Google Analytics cookies to measure visits and help us improve this website? You can change your choice using Analytics settings in the footer.') }}
            <a href="https://policies.google.com/privacy" target="_blank" rel="noopener noreferrer" class="underline">{{ __('Google\'s Privacy Policy') }}</a>
        </p>
        <div class="mt-4 flex flex-wrap gap-3">
            <button type="button" data-analytics-consent="granted" class="rounded-lg border border-newman-navy px-4 py-2 text-sm font-semibold hover:bg-newman-sand focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-newman-blue">
                {{ __('Allow analytics') }}
            </button>
            <button type="button" data-analytics-consent="denied" class="rounded-lg border border-newman-navy px-4 py-2 text-sm font-semibold hover:bg-newman-sand focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-newman-blue">
                {{ __('Decline') }}
            </button>
        </div>
    </section>
</template>
