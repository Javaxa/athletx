/* global $ */
var I18n = {
    language: "EN",
    load: function(callback) {
        var noCache = Math.random().toString(36).slice(-5);
        
        $.get("js/i18n.json?v=" + noCache, function(data) {
            var json = {};
            
            try {
                json = JSON.parse(data);
            } catch (e) {
                console.log(e);
            };
            
            I18n.data = json;
            if (typeof callback === "function") callback();
        }, "text");
        
        return I18n;
    },
    setCurrentLanguage: function(language) {
        if (this.getLanguages().indexOf(language) === -1) return false; // not an available language
        
        this.language = language;
        return I18n;
    },
    getLanguages: function() {
        return I18n.data ? I18n.data.availableLanguages : [];
    },
    getTranslations: function() {
        return I18n.data && I18n.data.translations ? I18n.data.translations : {};  
    },
    translate: function(key, _language) {
        var language = I18n.language;
        if (_language && I18n.getLanguages().indexOf(language) > -1) language = _language;
        
        var translations = I18n.getTranslations()[language] || {};
        var translation = translations[key];
        
        // no translation is avaialable, if this is another language, try to pull english version at least
        if (!translation && language != "EN") translation = I18n.translate(key, "EN");
        
        return translation;
    },
    translateNodes: function(dataAttributeName) {
        var nodes = $("[" + dataAttributeName + "]"); // find all elements that have that attribute (like data-i18n-key or something)
        nodes.each(function(index, el) {
            var key = $(el).attr(dataAttributeName);
            var translation = I18n.translate(key);
            
            if (translation) $(el).html(translation);
        });
    }
}
