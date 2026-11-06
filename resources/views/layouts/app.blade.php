<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'PT Pulo Kencana Drum Industri') }}</title>

        <!-- Google Fonts: Plus Jakarta Sans -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

        <!-- Scripts & Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <script data-navigate-once>
            (function () {
                const syncTheme = () => {
                    const isDark = localStorage.getItem('pkdi_theme') === 'dark' ||
                        (!('pkdi_theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches);
                    if (isDark) {
                        document.documentElement.classList.add('dark');
                    } else {
                        document.documentElement.classList.remove('dark');
                    }
                };
                syncTheme();

                const syncSidebarClass = () => {
                    document.documentElement.classList.remove('sidebar-animating');
                    if (localStorage.getItem('pkdi_desktop_sidebar') === 'false') {
                        document.documentElement.classList.add('sidebar-collapsed');
                    } else {
                        document.documentElement.classList.remove('sidebar-collapsed');
                    }
                };
                syncSidebarClass();

                document.addEventListener('livewire:navigating', () => {
                    syncTheme();
                    syncSidebarClass();
                });
                document.addEventListener('livewire:navigated', () => {
                    syncTheme();
                    syncSidebarClass();
                });

                document.addEventListener('livewire:navigate', (e) => {
                    if (window.__pkdiSkipFadeOut || (e.detail && e.detail.history)) {
                        window.__pkdiSkipFadeOut = false;
                        return;
                    }

                    const destUrl = e.detail && e.detail.url ? new URL(e.detail.url, window.location.origin) : null;
                    if (!destUrl) return;

                    if (destUrl.pathname === window.location.pathname && destUrl.search === window.location.search) {
                        return;
                    }

                    const mainEl = document.querySelector('main.page-transition');
                    if (!mainEl || typeof mainEl.animate !== 'function') {
                        return;
                    }

                    e.preventDefault();

                    // Slide sidebar active indicator immediately when tab is clicked
                    const navEl = document.querySelector('[x-ref="desktopNav"]');
                    if (navEl) {
                        const matchingLink = Array.from(navEl.querySelectorAll('a[href]')).find((a) => {
                            try {
                                return new URL(a.href, window.location.origin).pathname === destUrl.pathname;
                            } catch (_) {
                                return false;
                            }
                        });
                        if (matchingLink) {
                            window.__pkdiLastTabTop = matchingLink.offsetTop;
                            window.__pkdiLastTabHeight = matchingLink.offsetHeight;
                            const pill = navEl.querySelector('.pkdi-sliding-tab-pill');
                            if (pill) {
                                pill.classList.remove('!transition-none');
                                pill.style.transform = `translate3d(0, ${matchingLink.offsetTop}px, 0)`;
                                pill.style.height = `${matchingLink.offsetHeight}px`;
                            }
                        }
                    }

                    // Fade out old tab content in-place, then swap & fade in new tab content
                    const fadeOut = mainEl.animate(
                        [{ opacity: 1 }, { opacity: 0 }],
                        { duration: 200, easing: 'ease-in-out', fill: 'forwards' }
                    );

                    fadeOut.onfinish = () => {
                        window.__pkdiSkipFadeOut = true;
                        if (window.Livewire && typeof window.Livewire.navigate === 'function') {
                            window.Livewire.navigate(destUrl.toString());
                        } else if (window.Alpine && typeof window.Alpine.navigate === 'function') {
                            window.Alpine.navigate(destUrl.toString());
                        } else {
                            window.location.href = destUrl.toString();
                        }
                    };
                });
            })();
        </script>
    </head>
    <body class="font-sans text-[#0F172A] bg-[#F7F9FC] dark:bg-[#0F172A] dark:text-[#F8FAFC] antialiased min-h-full selection:bg-[#2563EB] selection:text-white overflow-x-hidden transition-colors duration-200">
        <div 
            x-data="{ 
                mobileSidebarOpen: false, 
                desktopSidebarOpen: localStorage.getItem('pkdi_desktop_sidebar') !== 'false',
                toggleDesktopSidebar() {
                    document.documentElement.classList.add('sidebar-animating');
                    this.desktopSidebarOpen = !this.desktopSidebarOpen;
                    localStorage.setItem('pkdi_desktop_sidebar', this.desktopSidebarOpen);
                    document.documentElement.classList.toggle('sidebar-collapsed', !this.desktopSidebarOpen);
                },
                expandDesktopSidebar() {
                    if (!this.desktopSidebarOpen) {
                        document.documentElement.classList.add('sidebar-animating');
                        this.desktopSidebarOpen = true;
                        localStorage.setItem('pkdi_desktop_sidebar', 'true');
                        document.documentElement.classList.remove('sidebar-collapsed');
                    }
                }
            }"
            class="min-h-screen bg-[#F7F9FC] dark:bg-[#0F172A] transition-colors duration-200"
        >
            <!-- Sidebar & Top Bar -->
            <livewire:layout.navigation />

            <!-- Main Content Area (pt-14 offsets the fixed h-14 Topbar so Topbar never scrolls away) -->
            <div class="pkdi-content-offset pt-14 flex flex-col min-h-screen">
                <main 
                    wire:key="page-{{ request()->route()?->getName() ?? request()->path() }}"
                    class="page-transition flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-5"
                >
                    @if (isset($header))
                        <div class="mb-5">
                            {{ $header }}
                        </div>
                    @endif

                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
