var profile = (function(){
    var copyOnly = function(filename, mid){
        var list = {
            "lib.profile.js": true,
            // we shouldn't touch our profile
            "package.json": true
        };
        return (mid in list) ||
            (/^app\/resources\//.test(mid)
            && !/\.css$/.test(filename)) ||
            /(png|jpg|jpeg|gif|tiff)$/.test(filename);
    };

    return {
        resourceTags: {
            copyOnly: function(filename, mid) {
                return copyOnly(filename, mid);
            },

            amd: function(filename, mid) {
                return /\.js$/.test(filename);
            }
        }
    };
})();