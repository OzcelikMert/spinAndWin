let Server = (function() {

    function Server(){}

    Server.isValidURL = function(url) {
        try {
            new URL(url);
        } catch (_) {
            return false;
        }
        return true;
    }

    Server.getURLMethods = function (name) {
        let methods={};
        location.search.replace(/[?&]+([^=&]+)=([^&]*)/gi,function(s,k,v){methods[k]=v})
        return name?methods[name]:methods;
    }

    Server.getPageName = function (){
        return window.location.pathname.split("/").pop().replace('.html', '').replace('.php', '');
    }

    return Server;
})();