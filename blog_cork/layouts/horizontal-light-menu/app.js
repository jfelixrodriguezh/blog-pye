var CORKUI = function() {
    let layoutName = 'Horizontal Light Menu';

    let Settings = {
        _admin: "Cork Admin Template",
        _layout_name: layoutName,
        _layout_darkMode: false,
        _layout_boxed: true,
        _layout_monochrome: false,
        _layout_altMenu: true,
        _layout_logo_dark: 'src/assets/img/logo.svg',
        _layout_logo_light: 'src/assets/img/logo2.svg',
        _reset: false,
    }

    let Colors = {
        _primary_50: "#f5f7fe", _primary_100: "#eceffe", _primary_200: "#cfd7fc", _primary_300: "#b1befa", _primary_400: "#778ef7", _primary_500: "#3d5df3", _primary_600: "#3754db", _primary_700: "#2e46b6", _primary_800: "#253892", _primary_900: "#1e2e77", _primary_1000: "#152143",
        _info_50: "#f2f9ff", _info_100: "#e6f4ff", _info_200: "#bfe3ff", _info_300: "#99d2ff", _info_400: "#4db0ff", _info_500: "#008eff", _info_600: "#0080e6", _info_700: "#006bbf", _info_800: "#005599", _info_900: "#00467d", _info_1000: "#0b2f52",
        _success_50: "#f2fbf6", _success_100: "#e6f6ee", _success_200: "#c0e9d4", _success_300: "#99dcbb", _success_400: "#4dc187", _success_500: "#01a754", _success_600: "#01964c", _success_700: "#017d3f", _success_800: "#016432", _success_900: "#005229", _success_1000: "#0c272b",
        _warning_50: "#fefaf4", _warning_100: "#fcf5e9", _warning_200: "#f8e5c8", _warning_300: "#f3d6a7", _warning_400: "#eab764", _warning_500: "#e19822", _warning_600: "#cb891f", _warning_700: "#a9721a", _warning_800: "#875b14", _warning_900: "#6e4a11", _warning_1000: "#282625",
        _danger_50: "#fdf5f6", _danger_100: "#fbeced", _danger_200: "#f6cfd2", _danger_300: "#f1b3b6", _danger_400: "#e67980", _danger_500: "#db4049", _danger_600: "#c53a42", _danger_700: "#a43037", _danger_800: "#83262c", _danger_900: "#6b1f24", _danger_1000: "#2c1c2b",
        _secondary_50: "#f9f5fc", _secondary_100: "#f2eafa", _secondary_200: "#dfcbf2", _secondary_300: "#cbacea", _secondary_400: "#a46edb", _secondary_500: "#7d30cb", _secondary_600: "#712bb7", _secondary_700: "#5e2498", _secondary_800: "#4b1d7a", _secondary_900: "#3d1863", _secondary_1000: "#1d1a3b",
        _dark_50: "#f5f5f5", _dark_100: "#eaeaec", _dark_200: "#cbcbcf", _dark_300: "#abacb2", _dark_400: "#6c6e78", _dark_500: "#2d303e", _dark_600: "#292b38", _dark_700: "#22242f", _dark_800: "#1b1d25", _dark_900: "#16181e", _dark_1000: "#181e2e"
    };

    var MediaSize = { xl: 1200, lg: 992, md: 991, sm: 576 };

    var Dom = {
        main: document.querySelector('html, body'),
        id: { container: document.querySelector("#container") },
        class: {
            navbar: document.querySelector(".navbar"),
            overlay: document.querySelector('.overlay'),
            search: document.querySelector('.toggle-search'),
            searchOverlay: document.querySelector('.search-overlay'),
            searchForm: document.querySelector('.search-form-control'),
            mainContainer: document.querySelector('.main-container'),
            mainHeader: document.querySelector('.header.navbar')
        }
    };

    var categoryScroll = {
        scrollCat: function() {
            var sidebarWrapper = document.querySelectorAll('.sidebar-wrapper li.active')[0];
            if (sidebarWrapper) {
                var sidebarWrapperTop = sidebarWrapper.offsetTop - 50;
                setTimeout(() => {
                    const scroll = document.querySelector('.menu-categories');
                    if (scroll) scroll.scrollTop = sidebarWrapperTop;
                }, 50);
            }
        }
    };

    var toggleFunction = {
        sidebar: function($recentSubmenu) {
            var sidebarCollapseEle = document.querySelectorAll('.sidebarCollapse');
            sidebarCollapseEle.forEach(el => {
                el.addEventListener('click', function (sidebar) {
                    sidebar.preventDefault();
                    let getSidebar = document.querySelector('.sidebar-wrapper');
                    if (getSidebar && $recentSubmenu === true) {
                        let subShow = document.querySelector('.collapse.submenu');
                        if (subShow && subShow.classList.contains('show')) {
                            subShow.classList.add('mini-recent-submenu');
                            getSidebar.querySelectorAll('.collapse.submenu').forEach(s => s.classList.remove('show'));
                        }
                    }
                    if (Dom.class.mainContainer) {
                        Dom.class.mainContainer.classList.toggle("sidebar-closed");
                        Dom.class.mainContainer.classList.toggle("sbar-open");
                    }
                    if (Dom.class.overlay) Dom.class.overlay.classList.toggle('show');
                    if (Dom.main) Dom.main.classList.toggle('sidebar-noneoverflow');
                });
            });
        },
        onToggleSidebarSubmenu: function() {
            let sidebar = document.querySelector('.sidebar-wrapper');
            if (!sidebar) return;
            ['mouseenter', 'mouseleave'].forEach(function(e){
                sidebar.addEventListener(e, function() {
                    // control hover submenú
                });
            });
        },
        offToggleSidebarSubmenu: function () {},
        overlay: function() {
            let dismiss = document.querySelector('#dismiss, .overlay');
            if (dismiss) {
                dismiss.addEventListener('click', function () {
                    if (Dom.class.mainContainer) {
                        Dom.class.mainContainer.classList.add('sidebar-closed');
                        Dom.class.mainContainer.classList.remove('sbar-open');
                    }
                    if (Dom.class.overlay) Dom.class.overlay.classList.remove('show');
                    if (Dom.main) Dom.main.classList.remove('sidebar-noneoverflow');
                });
            }
        },
        search: function() {
            if (Dom.class.search && Dom.class.searchOverlay) {
                Dom.class.search.addEventListener('click', function() {
                    this.classList.add('show-search');
                    Dom.class.searchOverlay.classList.add('show');
                    document.body.classList.add('search-active');
                });
                Dom.class.searchOverlay.addEventListener('click', function() {
                    this.classList.remove('show');
                    Dom.class.search.classList.remove('show-search');
                    document.body.classList.remove('search-active');
                });
                let close = document.querySelector('.search-close');
                if (close) {
                    close.addEventListener('click', function(e) {
                        e.stopPropagation();
                        Dom.class.searchOverlay.classList.remove('show');
                        Dom.class.search.classList.remove('show-search');
                        document.body.classList.remove('search-active');
                        if (Dom.class.searchForm) Dom.class.searchForm.value = '';
                    });
                }
            }
        },
        themeToggle: function () {
            var togglethemeEl = document.querySelector('.theme-toggle');
            if (togglethemeEl) {
                togglethemeEl.addEventListener('click', function() {
                    var getLocalStorageValue = sessionStorage.getItem("_LayoutDark");
                    var parseObj = JSON.parse(getLocalStorageValue);
                    if (parseObj) {
                        document.body.classList.remove('dark');
                        sessionStorage.setItem("_LayoutDark", false);
                    } else {
                        document.body.classList.add('dark');
                        sessionStorage.setItem("_LayoutDark", true);
                    }
                });
            }
        },
        profileSidebar: function() {
            let profileImage = document.querySelector('.user-profile-dropdown .user');
            let profileSidebarOverlay = document.querySelector('.psidebar-overlay');
            if (profileImage && profileSidebarOverlay) {
                profileImage.addEventListener('click', function() {
                    document.body.classList.add('profile-sidebar-active');
                    profileSidebarOverlay.classList.add('show');
                });
            }
        },
        profileSidebarClose: function() {
            let profileClose = document.querySelector('.profile-close span');
            let profileSidebarOverlay = document.querySelector('.psidebar-overlay');
            if (profileClose) {
                profileClose.addEventListener('click', function() {
                    document.body.classList.remove('profile-sidebar-active');
                    if (profileSidebarOverlay) profileSidebarOverlay.classList.remove('show');
                });
            }
            if (profileSidebarOverlay) {
                profileSidebarOverlay.addEventListener('click', function() {
                    document.body.classList.remove('profile-sidebar-active');
                    profileSidebarOverlay.classList.remove('show');
                });
            }
        },
        topbar: function(_toggleMode) {
            function setupDropdown(root) {
                const button = root.querySelector('.admin-menu-toggle');
                const menu = root.querySelector('.admin-dropdown-menu');
                if (!button || !menu) return;

                const showDropdown = async () => {
                    menu.classList.add('show');
                    button.classList.add('open');
                    button.setAttribute('aria-expanded', 'true');
                    if (window.FloatingUIDOM) {
                        const { computePosition, offset, flip, shift, autoUpdate } = window.FloatingUIDOM;
                        autoUpdate(button, menu, () => {
                            computePosition(button, menu, {
                                placement: 'bottom-start',
                                middleware: [offset(0), flip(), shift()],
                            }).then(({ x, y }) => {
                                Object.assign(menu.style, { left: `${x}px`, top: `${y}px`, position: 'absolute' });
                            });
                        });
                    }
                };

                const hideDropdown = () => {
                    menu.classList.remove('show');
                    button.classList.remove('open');
                    button.setAttribute('aria-expanded', 'false');
                };

                if (_toggleMode === 'click') {
                    button.addEventListener('click', (e) => {
                        e.stopPropagation();
                        const isOpen = menu.classList.contains('show');
                        closeAllDropdowns();
                        if (!isOpen) showDropdown();
                    });
                    document.addEventListener('click', closeAllDropdowns);
                }
                if (_toggleMode === 'hover') {
                    root.addEventListener('mouseenter', showDropdown);
                    root.addEventListener('mouseleave', hideDropdown);
                }
            }

            function closeAllDropdowns() {
                document.querySelectorAll('.admin-dropdown-menu.show').forEach(m => m.classList.remove('show'));
                document.querySelectorAll('.admin-menu-toggle.open').forEach(b => {
                    b.classList.remove('open');
                    b.setAttribute('aria-expanded', 'false');
                });
            }

            document.querySelectorAll('[data-toggle="dropdown"]').forEach(setupDropdown);
        }
    };

    var inBuiltfunctionality = {
        themeColor: function() {
            Object.entries(Colors).forEach(([k, v]) => sessionStorage.setItem(k, v));
            return Colors;
        },
        setLayoutName: function() {
            let slugify = Settings._layout_name.toLowerCase().replace(/\s+/g, '-');
            sessionStorage.setItem("_LayoutName", Settings._layout_name);
            sessionStorage.setItem("_LayoutName_Slugify", slugify);
            document.body.classList.add(`_${slugify}_`);
        },
        mainCatActivateScroll: function() {
            if (document.querySelector('.sidebar-wrapper .menu-categories') && window.PerfectScrollbar) {
                new PerfectScrollbar('.sidebar-wrapper .menu-categories', { wheelSpeed:.5, swipeEasing:!0, minScrollbarLength:40, maxScrollbarLength:300 });
            }
        },
        profileScroll: function() {
            if (document.querySelector('.profile-scroll') && window.PerfectScrollbar) {
                new PerfectScrollbar('.profile-scroll', { wheelSpeed:.5, swipeEasing:!0, minScrollbarLength:40, maxScrollbarLength:300 });
            }
        },
        notificationScroll: function() {
            if (document.querySelector('.notification-scroll') && window.PerfectScrollbar) {
                new PerfectScrollbar('.notification-scroll', { wheelSpeed:.5, swipeEasing:!0, minScrollbarLength:40, maxScrollbarLength:300 });
            }
        },
        messageScroll: function() {
            if (document.querySelector('.message-scroll') && window.PerfectScrollbar) {
                new PerfectScrollbar('.message-scroll', { wheelSpeed:.5, swipeEasing:!0, minScrollbarLength:40, maxScrollbarLength:300 });
            }
        },
        preventScrollBody: function() {},
        searchKeyBind: function() {
            let myModalEl = document.getElementById('searchDialog');
            if (myModalEl && window.Mousetrap && window.bootstrap) {
                Mousetrap.bind('ctrl+/', function() {
                    new bootstrap.Modal(myModalEl).show();
                    return false;
                });
            }
        },
        bsTooltip: function() {
            if (window.bootstrap) {
                document.querySelectorAll('.bs-tooltip').forEach(el => new bootstrap.Tooltip(el));
            }
        },
        bsPopover: function() {
            if (window.bootstrap) {
                document.querySelectorAll('.bs-popover').forEach(el => new bootstrap.Popover(el));
            }
        },
        onCheckandChangeSidebarActiveClass: function() {},
        MaterialRippleEffect: function() {
            if (window.Waves) {
                document.querySelectorAll('button.btn, a.btn').forEach(btn => {
                    if (!btn.classList.contains('_no--effects')) btn.classList.add('_effect--ripple');
                });
                if (document.querySelector('._effect--ripple')) {
                    Waves.attach('._effect--ripple', 'waves-light');
                    Waves.init();
                }
            }
        },
        functionalDropdown: function() {
            document.querySelectorAll('.more-dropdown .dropdown-item').forEach(item => {
                item.addEventListener('click', function() {
                    let title = document.querySelector('.more-dropdown .dropdown-toggle > span');
                    if (title) title.innerText = this.getAttribute('data-value');
                });
            });
        },
        EnableNavBarPopper: function() {
            if (window.bootstrap && window.bootstrap.Dropdown) {
                window.bootstrap.Dropdown.prototype._detectNavbar = function() { return false; };
            }
        },
        codeHighlighter: function() {},
        themeCustomizer: function() {
            let trigger = document.querySelector('.theme-customizer-trigger');
            let overlay = document.querySelector('.main-container .tc-overlay');
            if (!trigger || !overlay) return;

            trigger.addEventListener('click', function() {
                document.body.classList.add('theme-customizer-show');
                overlay.classList.add('show');
            });
            let closeBtn = document.querySelector('.tc-action-close .tc-btn-action-close');
            if (closeBtn) {
                closeBtn.addEventListener('click', function() {
                    document.body.classList.remove('theme-customizer-show');
                    overlay.classList.remove('show');
                });
            }
            overlay.addEventListener('click', function() {
                document.body.classList.remove('theme-customizer-show');
                overlay.classList.remove('show');
            });
        },
        monochrome: function() {},
        boxed: function() {
            if (Settings._layout_boxed) {
                document.body.classList.add('layout-boxed');
            }
        },
        mode: function() {
            let isDark = JSON.parse(sessionStorage.getItem("_LayoutDark"));
            if (isDark) {
                document.body.classList.add('dark');
            } else {
                document.body.classList.remove('dark');
            }
        },
        SearchFunctionality: function() {
            const searchInput = document.getElementById('searchInput');
            if (!searchInput) return;
            searchInput.addEventListener('input', function () {
                const query = this.value.toLowerCase().trim();
                document.querySelectorAll('.searchable-list-content .searchable-list .search-list-item').forEach(item => {
                    item.style.display = item.textContent.toLowerCase().includes(query) ? 'flex' : 'none';
                });
            });
        },
        dynaicFooterDate: function() {
            let yearEl = document.querySelector(".dynamic-year");
            if (yearEl) yearEl.textContent = new Date().getFullYear();
        }
    };

    var _mobileResolution = {
        onRefresh: function() {
            if (window.innerWidth <= MediaSize.md) {
                categoryScroll.scrollCat();
                toggleFunction.sidebar();
            }
        },
        onResize: function() {
            window.addEventListener('resize', function() {
                if (window.innerWidth <= MediaSize.md) toggleFunction.offToggleSidebarSubmenu();
            });
        }
    };

    var _desktopResolution = {
        onRefresh: function() {
            if (window.innerWidth > MediaSize.md) {
                categoryScroll.scrollCat();
                toggleFunction.sidebar();
                toggleFunction.onToggleSidebarSubmenu();
            }
        },
        onResize: function() {
            window.addEventListener('resize', function() {
                if (window.innerWidth > MediaSize.md) toggleFunction.onToggleSidebarSubmenu();
            });
        }
    };

    function sidebarFunctionality() {
        if (Dom.id.container) {
            if (window.innerWidth <= 991) {
                Dom.id.container.classList.add("sidebar-closed");
            } else {
                Dom.id.container.classList.remove("sidebar-closed");
            }
        }
    }

    return {
        init: function(Layout) {
            toggleFunction.overlay();
            toggleFunction.search();
            toggleFunction.topbar("hover");
            toggleFunction.themeToggle(Layout);
            toggleFunction.profileSidebar();
            toggleFunction.profileSidebarClose();

            _desktopResolution.onRefresh();
            _desktopResolution.onResize();
            _mobileResolution.onRefresh();
            _mobileResolution.onResize();

            sidebarFunctionality();

            inBuiltfunctionality.themeColor();
            inBuiltfunctionality.setLayoutName();
            inBuiltfunctionality.mainCatActivateScroll();
            inBuiltfunctionality.profileScroll();
            inBuiltfunctionality.notificationScroll();
            inBuiltfunctionality.messageScroll();
            inBuiltfunctionality.searchKeyBind();
            inBuiltfunctionality.bsTooltip();
            inBuiltfunctionality.bsPopover();
            inBuiltfunctionality.MaterialRippleEffect();
            inBuiltfunctionality.functionalDropdown();
            inBuiltfunctionality.EnableNavBarPopper();
            inBuiltfunctionality.themeCustomizer();
            inBuiltfunctionality.boxed();
            inBuiltfunctionality.mode();
            inBuiltfunctionality.SearchFunctionality();
            inBuiltfunctionality.dynaicFooterDate();
        }
    };
}();

window.addEventListener('DOMContentLoaded', function() {
    CORKUI.init('layout');
});