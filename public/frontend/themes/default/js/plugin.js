(function () {
    const script = document.currentScript;
    const clientId = script.getAttribute('client-id');
    const token = script.getAttribute('token');

    const iframe = document.createElement('iframe');
    iframe.src = `https://yourdomain.com/plugin/search?client_id=${clientId}&token=${token}`;
    iframe.width = '100%';
    iframe.height = '550';
    iframe.frameBorder = '0';
    iframe.style = 'border:1px solid #ddd;border-radius:6px;';
    iframe.title = 'Tyre Booking Plugin';

    script.parentNode.insertBefore(iframe, script.nextSibling);
})();
