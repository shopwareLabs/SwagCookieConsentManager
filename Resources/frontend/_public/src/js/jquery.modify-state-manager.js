(function($, window, document) {
    /**
     * @returns { boolean }
     */
    function hasCookiesAllowed () {
        if (window.cookieRemoval === 0) {
            return true;
        }

        if (window.cookieRemoval === 1) {
            if (document.cookie.indexOf('cookiePreferences') !== -1) {
                return true;
            }

            return document.cookie.indexOf('cookieDeclined') === -1;
        }

        // Must be cookieRemoval = 2, so only depends on existence of `allowCookie`
        return document.cookie.indexOf('allowCookie') !== -1;
    }

    /**
     * @returns { boolean }
     */
    function isDeviceCookieAllowed () {
        var cookiesAllowed = hasCookiesAllowed();

        if (window.cookieRemoval !== 1) {
            return cookiesAllowed;
        }

        return cookiesAllowed && document.cookie.indexOf('"name":"x-ua-device","active":true') !== -1;
    }

    window.StateManager._setDeviceCookie = function() {
        if (!isDeviceCookieAllowed()) {
            return;
        }

        var device = this._getCurrentDevice(),
            cookieString = 'x-ua-device=' + device + '; path=/';

        if (window.secureShop !== undefined && window.secureShop === true) {
            cookieString = 'x-ua-device=' + device + ';secure; path=/';
        }

        document.cookie = cookieString;
    };
})(jQuery, window, document);
