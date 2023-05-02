pimcore.registerNS("pimcore.plugin.HelloBundle");

pimcore.plugin.HelloBundle = Class.create(pimcore.plugin.admin, {
    getClassName: function () {
        return "pimcore.plugin.HelloBundle";
    },

    initialize: function () {
        pimcore.plugin.broker.registerPlugin(this);
    },

    pimcoreReady: function (params, broker) {
        // alert("HelloBundle ready!");
    }
});

var HelloBundlePlugin = new pimcore.plugin.HelloBundle();
