//define template
var template = $('#sections .section:first').clone();

//define counter
var sectionsCount = 1;

//add new section
$('body').on('click', '.addsection', function() {

    //increment
    sectionsCount++;

    //loop through each input
    var section = template.clone().find(':input').each(function(){

        //set id to store the updated section number
        var newId = this.id + sectionsCount;

        //update for label
        $(this).prev().attr('for', newId);

        //update id
        this.id = newId;
        this.value = '';
        if ($(this).hasClass('qty')) {
            this.value = 1;
        }

    }).end()

    //inject new section
    .insertBefore('#add_more');
    return false;
});

//add value
$('body').on('click', '.addValue', function() {
    $('.qty').val(1);
});

//remove section
$('#sections').on('click', '.remove', function() {
    //fade out section
    $(this).parent().fadeOut(300, function(){
        //remove parent element (main section)
        $(this).parent().parent().empty();
        calculateAmount();
        return false;
    });
    return false;
});
