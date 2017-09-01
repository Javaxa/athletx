

function setCookie(name, value, expirySeconds) {
    var expiry = new Date().getTime() + expirySeconds * 1000; // convert seconds to milliseconds
    expiry = new Date(expiry);

    var expires = "; expires=" + expiry.toUTCString();

    document.cookie = name + "=" + value + expires + "; path=/";
}

function getCookie(name) {
    var value = "; " + document.cookie;
    var parts = value.split("; " + name + "=");
    if (parts.length == 2) return parts.pop().split(";").shift();
}

// Language translation
I18n.load(function() {
	// create language buttons based on what languages are available in i18n.json
	var languages = I18n.getLanguages();
	var currentLanguage = getCookie("language") || "EN";
	
	// map languages and convert them to buttons, then add "/" between them
	var languageSelections = languages.map(function(language) {
		return "<span class='language-button' data-language='" + language + "'>" + language + "</span>";
	}).join("<h>/</h>");
	
	$(".languageselect").html(languageSelections);
	$(".language-button[data-language='" + currentLanguage + "']").click(); // trigger a click on the language button to translate the items
});

$(document).on("click", ".language-button", function() {
	var language = $(this).text();
	setCookie("language", language, 3650 * 24 * 3600); // 10 years lol
	
	$(this).closest(".languageselect").find(".language-button").removeClass("selected"); // unselect currently selected language
	$(this).addClass("selected");
	
	I18n.setCurrentLanguage(language).translateNodes("data-i18n");
	
	$("body").attr("class", "language-" + language.toLowerCase()); // language-ge, language-en, language-fr idk
});



$(document).on("click", "[data-href]", function(e) {
	var item = $(this).closest("[data-href]");
	var href = item.attr("data-href");
	
	location.href = "#" + href;
	
	e.cancelBubbles = true;
	e.stopPropagation();
});


//store sections menu



//male start//
$(".malesection").hover(function() {
  $(".dropdownsectionmale").toggleClass("active");
});

$(".dropdownsectionmale").hover(function() {
  $(".dropdownsectionmale").toggleClass("active");
});
//male end//

//female start//
$(".femalesection").hover(function() {
  $(".dropdownsectionfemale").toggleClass("active");
});

$(".dropdownsectionfemale").hover(function() {
  $(".dropdownsectionfemale").toggleClass("active");
});
//female end//



//menu toggle and setting

$(".menu label.menu-toggle").click(function() {
  $(".menu label.menu-toggle").toggleClass("active");
});


$(document).ready(function() {
    $("#menu").prop("checked", true);
});


//slider js

jQuery(document).ready(function ($) {

  $('#checkbox').ready(function(){
    setInterval(function () {
        moveRight();
    }, 5000);
  });
  
	var slideCount = $('#slider ul li').length;
	var slideWidth = $('#slider ul li').width();
	var slideHeight = $('#slider ul li').height();
	var sliderUlWidth = slideCount * slideWidth;
	
	$('#slider').css({ width: slideWidth, height: slideHeight });
	
	$('#slider ul').css({ width: sliderUlWidth, marginLeft: - slideWidth });
	
    $('#slider ul li:last-child').prependTo('#slider ul');

    function moveLeft() {
        $('#slider ul').animate({
            left: + slideWidth
        }, 200, function () {
            $('#slider ul li:last-child').prependTo('#slider ul');
            $('#slider ul').css('left', '');
        });
    };

    function moveRight() {
        $('#slider ul').animate({
            left: - slideWidth
        }, 200, function () {
            $('#slider ul li:first-child').appendTo('#slider ul');
            $('#slider ul').css('left', '');
        });
    };

    $('a.control_prev').click(function () {
        moveLeft();
    });

    $('a.control_next').click(function () {
        moveRight();
    });
});



$(".nutritionplanorder").click(function() {
  $(".nutritionplanorder").addClass("active");
});

$(".window-close-button").click(function(e) {
  e.preventDefault();
  e.stopPropagation();
  $(".nutritionplanorder").removeClass("active");
});