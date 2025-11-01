/**
 *
 * Main App Component
 *
 */
"use strict";
/* global app, PostsPaginator, toastr */

// Init
$(function() {

    // eslint-disable-next-line no-console
    console.info('🚀 JustJobs  Loaded 🚀');

    // Instantiating default actions if installed
    if (typeof app !== 'undefined') {

        if (app.showCookiesBox !== null) {

            window.cookieconsent.initialise({
                "theme": "classic",
                "position": "bottom-right",
                "palette": {
                    "popup": {
                        "background": "#efefef",
                        "text": "#404040"
                    },
                    "button": {
                        "background": "#007BFF",
                        "text": "#ffffff"
                    }
                },
                content: {
                    "message": '🍪 ' + trans('This website uses cookies to improve your experience.'),
                    "dismiss": trans("Got it!"),
                    "link": trans("Learn more"),
                    "href": "http://cookies.insites.com/about-cookies"
                },
            });
        }

        // Check if age verification dialog should be enabled
        if (app.enable_age_verification_dialog &&
            !isSlugInUrl(app.tosPageSlug) &&
            !isSlugInUrl(app.privacyPageSlug) &&
            window.location.href.indexOf('invoices') === -1) {

            const classes = 'body .flex-fill, footer, .global-announcement-banner, .navbar';

            if (!getCookie('site_entry_approval')) {
                // Show modal and add blur class to multiple elements
                $('#site-entry-approval-dialog').modal('show');
                const elementsToBlur = $(classes);
                elementsToBlur.addClass('blurred');
            }

            // Remove blur class when modal is hidden
            $('#site-entry-approval-dialog').on('hidden.bs.modal', function () {
                const elementsToBlur = $(classes);
                elementsToBlur.removeClass('blurred');
            });
        }

        // Auto-including the CSRF token in all AJAX Requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
        });

        // Globally handling AJAX requests, especially for handling expired tokens and sesisions
        $(document).ajaxError(function (event, jqXHR) {
            if (jqXHR.status === 0) {
                // eslint-disable-next-line no-console
                console.error('Not connect.n Verify Network.');
            } else if (jqXHR.status === 404) {
                // eslint-disable-next-line no-console
                console.error('Requested page not found. [404]');
            } else if (jqXHR.status === 500) {
                // eslint-disable-next-line no-console
                console.error('Internal Server Error [500].');
            } else if (jqXHR.status === 401) {
                // eslint-disable-next-line no-console
                console.error('Session expired. Redirecting you to refresh the session.');
                redirect(app.baseUrl);
            } else if (jqXHR.status === 408) {
                reload();
            } else {
                // eslint-disable-next-line no-console
                console.error('Uncaught Error.n' + jqXHR.responseText);
            }
        });

        // Displaying error messages for expired sessions
        if (app.sessionStatus === 'expired') {
            toastr.options.closeButton = true;
            toastr.info('Session expired', 'Page refreshed');
        }

        $('.dark-mode-switcher').on('click', function () {
            let currentTheme = getCookie('app_theme');
            if ((currentTheme !== null && currentTheme === 'dark') || app.theme === 'dark') {
                setCookie('app_theme', 'light', 365);
            } else {
                setCookie('app_theme', 'dark', 365);
            }
            reload();
        });

        if (app.sessionStatus === 'expired') {
            toastr.options.closeButton = true;
            toastr.info('Session expired', 'Page refreshed');
        }

        $('.rtl-mode-switcher').on('click', function () {
            let currentTheme = getCookie('app_rtl');
            if (currentTheme === 'rtl') {
                setCookie('app_rtl', 'ltr', 365);
            } else {
                setCookie('app_rtl', 'rtl', 365);
            }
            reload();
        });

        initTooltips();

        if (window.location.href.indexOf('register') >= 0) {
            // Forcing TOS checkbox for social auth
            $('.social-login-links a').on('click', function (event) {
                if ($('#tosAgree').is(':checked') === false) {
                    event.preventDefault();
                    $('#tosAgree').addClass('is-invalid');
                }
            });
        }
    }
});

/**
 * Log function sugar syntax
 * @param v
 * @param type
 */
// eslint-disable-next-line no-unused-vars
function log(v,type = 'log') {
    if(typeof app !== 'undefined' && app.debug){
        switch (type) {
        case 'info':
            // eslint-disable-next-line no-console
            console.info(v);
            break;
        case 'log':
            // eslint-disable-next-line no-console
            console.log(v);
            break;
        case 'warn':
            // eslint-disable-next-line no-console
            console.warn(v);
            break;
        case 'error':
            // eslint-disable-next-line no-console
            console.error(v);
            break;
        }
    }
    return true;
}

/**
 * Redirect function
 * @param url
 */
function redirect(url) {
    window.location.href = url;
}

/**
 * Submits the search form
 */
// eslint-disable-next-line no-unused-vars
function submitSearch() {
    $('.search-box-wrapper').submit();
}

/**
 * Page reload function
 */
function reload() {
    return window.location.reload();
}

/**
 * Instantiates tooltips
 */
function initTooltips(){
    $('[data-toggle="tooltip"]').tooltip();
    $('.to-tooltip').tooltip();
}

/**
 * Copy to clipboard function
 * @param textToCopy
 */
function copyToClipboard(textToCopy, container = 'body') {
    let $temp = $("<textarea>");
    $(container).append($temp);
    $temp.val(textToCopy).select();
    document.execCommand("copy");
    $temp.remove();
}

/**
 * Attaches scroll handlers & sticky behaviour to desired components
 * @param component
 * @param stickyClass
 */
// eslint-disable-next-line no-unused-vars
function initStickyComponent(component,stickyClass) {
    let sticky = false;
    let top = $(window).scrollTop();
    if ($(".main-wrapper").offset().top < top) {
        $(component).addClass(stickyClass);
        // eslint-disable-next-line no-unused-vars
        sticky = true;
    } else {
        $(".side-menu, .suggestions-box").removeClass(stickyClass);
    }
}

/**
 * Go to login via UI redirect
 */
// eslint-disable-next-line no-unused-vars
function goToLogin() {
    redirect(app.baseUrl + '/login');
}

/**
 * Accepts adult content confirm dialog
 */
// eslint-disable-next-line no-unused-vars
function acceptSiteEntry() {
    setCookie('site_entry_approval',true,90);
    $('#site-entry-approval-dialog').modal('hide');
}

/**
 * Set cookie
 * @param key
 * @param value
 * @param expiry
 */
function setCookie(key, value, expiry) {
    var expires = new Date();
    expires.setTime(expires.getTime() + (expiry * 24 * 60 * 60 * 1000));
    document.cookie = key + '=' + value + ';expires=' + expires.toUTCString() + ';path=/';
}

/**
 * Get cookie value
 * @param key
 * @returns {any}
 */
function getCookie(key) {
    var keyValue = document.cookie.match('(^|;) ?' + key + '=([^;]*)(;|$)');
    return keyValue ? keyValue[2] : null;
}

/**
 * Delete cookie
 * @param key
 */
// eslint-disable-next-line no-unused-vars
function eraseCookie(key) {
    var keyValue = getCookie(key);
    setCookie(key, keyValue, '-1');
}

/**
 * Reload themes on the fly
 */
// eslint-disable-next-line no-unused-vars
function reloadTheme() {
    let appTheme = 'css/bootstrap/bootstrap';
    let currentTheme = getCookie('app_theme');
    let currentRTLSetting = getCookie('app_rtl');
    if (currentRTLSetting === 'rtl') {
        appTheme += '.rtl';
    }

    if (currentTheme === 'dark') {
        appTheme += '.dark';
    }
    appTheme += ".css";
    $('#app-theme').attr('href', appTheme);
}

/**
 * Launches custom, stackable and dismisable toasts
 * @param type
 * @param title
 * @param message
 * @param subtitle
 */
function launchToast(type, title, message, subtitle = '') {
    $.toast({
        type: '',
        title: title,
        subtitle: subtitle,
        content: message,
        dismissible: true,
        indicator: {
            type: type
        },
        delay: 5000,
    });
}

/**
 * Opens up device share API or fallbacks to URL copy
 * @param url
 */
// eslint-disable-next-line no-unused-vars
function shareOrCopyLink(url = false) {
    if (url === false) {
        url = window.location.href;
    }
    if (navigator.share) {
        navigator.share({
            title: document.title,
            url: url
        })
            // eslint-disable-next-line no-console
            .then(() => console.log('Successful share'))
            // eslint-disable-next-line no-console
            .catch(error => console.log('Error sharing:', error));
    } else {
        copyToClipboard(url);
        launchToast('success', trans('Success'), trans('Link copied to clipboard')+'.', 'now');
    }
}

/**
 * Auto Adjusts textareas on resize
 * @param el
 */
// eslint-disable-next-line no-unused-vars
function textAreaAdjust(el) {
    el.style.height = (el.scrollHeight > el.clientHeight) ? (el.scrollHeight) + "px" : "45px";
}

/**
 * Filters up user received notifications ( via sockets )
 * @returns {string}
 */
// eslint-disable-next-line no-unused-vars
function getNotificationsActiveFilter() {
    let activeType = '';
    // get active filter if exists
    if (window.location.href.indexOf('/likes') >= 0) {
        activeType = '/likes';
    } else if (window.location.href.indexOf('/messages') >= 0) {
        activeType = '/messages';
    } else if (window.location.href.indexOf('/subscriptions') >= 0) {
        activeType = '/subscriptions';
    } else if (window.location.href.indexOf('/tips') >= 0) {
        activeType = '/tips';
    } else if (window.location.href.indexOf('/promos') >= 0) {
        activeType = '/promos';
    }

    return activeType;
}

/**
 * Method used for translating locale strings
 * @param key
 * @param replace
 * @returns {T|*}
 */
// eslint-disable-next-line no-unused-vars
function trans(key, replace = {})
{
    let translation = window.translations[key];
    if(translation === null || typeof translation === 'undefined'){ // If no translation available, return the ( default - en ) key
        return key;
    }
    for (var placeholder in replace) {
        translation = translation.replace(`:${placeholder}`, replace[placeholder]);
    }
    if(typeof translation === 'undefined'){
        return key;
    }
    return translation;
}

/**
 * Method used for translating locale strings
 * Supports multiple choices translations
 * @param key
 * @param replace
 * @returns {T|*}
 */
// eslint-disable-next-line no-unused-vars
function trans_choice(key, count = 1, replace = {})
{
    let keyValue = window.translations[key];
    if(typeof keyValue === 'undefined'){
        return key;
    }
    const translations = keyValue.split('|');
    let translation = count > 1 || count === 0 ? translations[1] : translations[0];

    for (var placeholder in replace) {
        translation = translation.replace(`:${placeholder}`, replace[placeholder]);
    }
    return translation;
}

/**
 * Updates button state, adding loading icon to it and disabling it
 * @param state
 * @param buttonElement
 */
// eslint-disable-next-line no-unused-vars
function updateButtonState(state, buttonElement, buttonContent = false, loadingColor = 'primary'){
    if(state === 'loaded'){
        if(buttonContent){
            buttonElement.html(buttonContent);
        }
        else{
            buttonElement.html('<div class="d-flex justify-content-center align-items-center"><ion-icon name="paper-plane"></ion-icon></div>');
        }
        buttonElement.removeClass('disabled');
    }
    else{
        buttonElement.html(`<div class="d-flex justify-content-center align-items-center">
            <div class="spinner-border text-${loadingColor} spinner-border-sm" role="status">
            <span class="sr-only">${trans('Loading...')}</span>
            </div>
            ${(buttonContent !== false ? '<div class="ml-2">'+buttonContent+'</div>' : '')}
            </div>`);
        buttonElement.addClass('disabled');
    }
}

/**
 * Re-sends the user email verification
 * @param callback
 */
// eslint-disable-next-line no-unused-vars
function sendEmailConfirmation(callback = function(){}){
    $('.unverified-email-box').attr('onClick','');
    $.ajax({
        url:app.baseUrl +'/resendVerification',
        type:'POST',
        success : function(){
            $('.unverified-email-box').fadeOut();
            launchToast('success', trans('Success'), trans('Confirmation email sent. Please check your inbox and spam folder.'), 'now');
            callback();
        },
        error: function () {

        }
    });
}

/**
 * Preps a data beacon data sample, to be saved before page unload
 * @returns {FormData}
 */
// eslint-disable-next-line no-unused-vars
function prepBeaconDataSample(){
    var fd = new FormData();
    fd.append('prevPage', PostsPaginator.currentPage);
    return fd;
}

/**
 * Returns current bootstrap breakpoint to the JS side
 * @returns {{name: (string|string), index: number}|null}
 */
// eslint-disable-next-line no-unused-vars
function bootstrapDetectBreakpoint() {
    // cache some values on first call
    let breakpointNames = ["xl", "lg", "md", "sm", "xs"];
    let breakpointValues = [];
    for (const breakpointName of breakpointNames) {
        breakpointValues[breakpointName] = window.getComputedStyle(document.documentElement).getPropertyValue('--breakpoint-' + breakpointName);
    }
    let i = breakpointNames.length;
    for (const breakpointName of breakpointNames) {
        i--;
        if (window.matchMedia("(min-width: " + breakpointValues[breakpointName] + ")").matches) {
            return {name: breakpointName, index: i};
        }
    }
    return null;
}

/**
 * Posts user report
 * @param job_id
 * @param company_id
 * @param type
 * @param details
 */
function postReport(job_id,company_id,type,details){
    $.ajax({
        type: 'POST',
        data: {
            job_id,
            company_id,
            type,
            details,
        },
        url: app.baseUrl + '/report/content',
        success: function () {
            launchToast('success',trans('Success'),trans('Report submitted'));
            $('#report-user-post').modal('hide');
            $('#post_report_details').val('');
        },
        error: function (result) {
            launchToast('danger',trans('Error'),result.responseJSON.message);
        }
    });
}


/**
 * Shows up user report box
 * @param job_id
 * @param company_id
 */
// eslint-disable-next-line no-unused-vars
function showReportBox(job_id, company_id ){
    let dialogElement = $('#report-user-post');
    dialogElement.modal('show');
    $('.submit-report-button').unbind();
    $('.submit-report-button').on('click',function () {
        postReport(job_id, company_id ,$('#reasonExamples').val(),$('#post_report_details').val());
    });
}

// eslint-disable-next-line no-unused-vars
function copyJobUrl(text) {
    copyToClipboard(text);
    launchToast('success', trans('Success'), trans('Link copied to clipboard')+'.', 'now');
}

// eslint-disable-next-line no-unused-vars
function openLanguageSelectorDialog() {
    $('#language-selector-dialog').modal('show');
}

// eslint-disable-next-line no-unused-vars
function setUserLanguage() {
    let languageLink = app.baseUrl + '/language/' + $('#language_code').val();
    window.location.href = languageLink;
}

// eslint-disable-next-line no-unused-vars
function getWebsiteFormattedAmount(amount){
    let currencyPosition = app.currencyPosition;
    let currency = app.currencySymbol;

    return currencyPosition === 'left' ? currency + amount : amount + currency;
}

// eslint-disable-next-line no-unused-vars
function getTaxDescription(taxName, taxPercentage, taxType){
    if(taxType !== 'fixed') {
        let type = taxType === 'inclusive' ? ' incl.' : '';
        return taxName + " (" + taxPercentage + "%" + type + ")";
    }
    return taxName;
}

/**
 * Binding ctrl+s event to form submissions
 * @param formSelector
 */
// eslint-disable-next-line no-unused-vars
function ctrlSaveInit(formSelector) {
    $(document).keydown(function(e) {
        var key = undefined;
        var possible = [ e.key, e.keyIdentifier, e.keyCode, e.which ];
        while (key === undefined && possible.length > 0) {
            key = possible.pop();
        }
        if (key && (key === 115 || key === 83 ) && (e.ctrlKey || e.metaKey) && !(e.altKey)) {
            e.preventDefault();
            $(formSelector).submit();
            return false;
        }
        return true;
    });
}

// eslint-disable-next-line no-unused-vars
function dimissGlobalAnnouncement(id) {
    $.ajax({
        url:app.baseUrl +'/markBannerAsSeen',
        type:'POST',
        data : {id: id},
        success : function(){
            // Placeholders
        },
        error: function () {
            // Placeholders
        }
    });
}

// Function to check if URL contains specific slug
function isSlugInUrl(slug) {
    return slug !== null && window.location.href.indexOf(slug) >= 0;
}
