pimcore.registerNS("pimcore.plugin.MedilabBundle");

pimcore.plugin.MedilabBundle = Class.create(pimcore.plugin.admin, {
    getClassName: function () {
        return "pimcore.plugin.MedilabBundle";
    },

    initialize: function () {
        pimcore.plugin.broker.registerPlugin(this);
    },

    pimcoreReady: function (params, broker) {
        // alert("MedilabBundle ready!");
    }
});

var MedilabBundlePlugin = new pimcore.plugin.MedilabBundle();
