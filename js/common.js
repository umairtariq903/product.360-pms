function isValidEmail(email) {
    // E-posta adresi için geçerli bir regex deseni
    var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    return emailPattern.test(email);
}

function GonderilecekMailKontrol() {
    Page.Ajax.Get("ajax").Send("GonderilecekMailKontrol",null,function (resp) {
        console.log(resp);
    },"");
}

function GetOperatingSystem() {
    var userAgent = navigator.userAgent || navigator.vendor || window.opera;

    if (/iPad|iPhone|iPod/.test(userAgent) && !window.MSStream) {
        return 'iOS';
    } else if (/android/i.test(userAgent)) {
        return 'Android';
    } else {
        return 'unknown';
    }
}

$(function () {
    var client = new ClientJS(); // Create A New Client Object
    var ua = client.getBrowserData().ua;
    var canvasPrint = client.getCanvasPrint();

    var fingerprint = client.getCustomFingerprint(ua, canvasPrint);

    Cookie.set("_dgr_cljs",fingerprint,"/");
    Page.Ajax.Get("act=ajax").Send("BeniHatirlaDogrula",null,function (resp) {
        if (resp == 1)
            Page.Load(SITE_URL);
    },"");
})
