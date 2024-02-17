//Delay Class => .animate__faster added
//  class="animate__animated animate__faster"
// <link rel="stylesheet" href="animate.min.css"/>
let CustomPopUp = (function() {
    let element_id, showing_animation_class, closing_animation_class;

    function CustomPopUp(element_id, showing_animation_class, closing_animation_class) {
        this.element_id = element_id;
        this.showing_animation_class = showing_animation_class;
        this.closing_animation_class = closing_animation_class;
        $(this.element_id).addClass("animate__animated animate__faster")
    }

    function setElementStatusEffect(element, add_class, remove_class){
        element.addClass(add_class);
        element.removeClass(remove_class);
    }

    CustomPopUp.prototype.open = function() {
        try{
            setElementStatusEffect($(this.element_id), this.showing_animation_class, this.closing_animation_class);
            $(this.element_id).show(0);
        }catch (exception){ return exception;}
    }

    CustomPopUp.prototype.close = function() {

        try{
            setElementStatusEffect($(this.element_id), this.closing_animation_class, this.showing_animation_class);
            $(this.element_id).delay(500).hide(0);
        }catch (exception){ return exception;}
    }

    return CustomPopUp;
})();


