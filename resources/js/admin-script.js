/* Admin script */
jQuery(function($) {
    // Switches option sections
    $('.group').hide();
    var activetab = '';
    if ('undefined' != typeof localStorage) {
        activetab = localStorage.getItem('activetab');
    }
    if ('' != activetab && $(activetab).length) {
        $( activetab ).fadeIn();
    } else {
        $('.group:first').fadeIn();
    }
    $('.group .collapsed').each(function() {
        $( this )
            .find('input:checked')
            .parent()
            .parent()
            .parent()
            .nextAll()
            .each( function() {
            if ($( this ).hasClass('last')) {
                $(this).removeClass('hidden');
                return false;
            }
            $(this)
                .filter('.hidden')
                .removeClass('hidden');
        });
    });

    if ('' != activetab && $(activetab + '-tab').length) {
        $(activetab + '-tab').addClass('nav-tab-active');
    } else {
        $('.nav-tab-wrapper a:first').addClass('nav-tab-active');
    }
    $('.nav-tab-wrapper a').click(function(evt) {
        $('.nav-tab-wrapper a').removeClass('nav-tab-active');
        $(this)
            .addClass('nav-tab-active')
            .blur();
        var clicked_group = $(this).attr('href');
        if ('undefined' != typeof localStorage) {
            localStorage.setItem('activetab', $(this).attr('href'));
        }
        $('.group').hide();
        $(clicked_group).fadeIn();
        evt.preventDefault();
    });
    
    // Flag preview section
    var basicOptionSection = document.getElementById('queerify_basic');
    var $imgPath = phpData.imgPath;
    var $flag = phpData.flag;
    var fullPath = $imgPath + $flag + '.png';
    var flagImg = document.createElement('img');
    flagImg.className = 'flag';
    basicOptionSection.appendChild(flagImg);
    $(flagImg).attr('src', fullPath);
    
});