$(document).ready(function(){

   
    $("#manufactureDate").inputmask("9999-99-99");
    $("#expiryDate").inputmask("9999-99-99");
    $("#date3").inputmask("99 December, 9999");


    // Email

    $("#email").inputmask(
        {
            mask:"*{1,20}[.*{1,20}][.*{1,20}][.*{1,20}]@*{1,20}[.*{2,6}][.*{1,2}]",
            greedy:!1,onBeforePaste:function(m,a){return(m=m.toLowerCase()).replace("mailto:","")},
            definitions:{"*":
                {
                    validator:"[0-9A-Za-z!#$%&'*+/=?^_`{|}~-]",
                    cardinality:1,
                    casing:"lower"
                }
            }
        }
    )

    // Phone Number
    $("#ph-number").inputmask({mask:"(999) 999-9999"});

    // Currency
    $("#prisce").inputmask({mask:"RM999"});

    /*
    ==================
        METHODS
    ==================
    */


    // On Complete
    $("#oncomplete").inputmask("99/99/9999",{ oncomplete: function(){ $('#oncompleteHelp').css('display', 'block'); } });


    // On InComplete
    $("#onincomplete").inputmask("99/99/9999",{ onincomplete: function(){ $('#onincompleteHelp').css('display', 'block'); } });

    
    // On Cleared
    $("#oncleared").inputmask("99/99/9999",{ oncleared: function(){ $('#onclearedHelp').css('display', 'block'); } });
    

    // isComplete

    $("#isComplete").inputmask({mask:"999.999.999.99"})
    $("#isComplete").inputmask("setvalue", "117.247.169.64");
    $('#isComplete').on('focus keyup', function(event) {
        event.preventDefault();
        if($(this).inputmask("isComplete")){
            $('#isCompleteHelp').css('display', 'block');
        }
    });
    $('#isComplete').on('keyup', function(event) {
        event.preventDefault();
        if(!$(this).inputmask("isComplete")){
            $('#isCompleteHelp').css('display', 'none');
        }
    });


    // Set Default Value

    $("#setVal").inputmask({
        mask:"*{1,20}[.*{1,20}][.*{1,20}][.*{1,20}]@*{1,20}[.*{2,6}][.*{1,2}]",
        greedy:!1,onBeforePaste:function(m,a){return(m=m.toLowerCase()).replace("mailto:","")},
        definitions:{"*":
            {
                validator:"[0-9A-Za-z!#$%&'*+/=?^_`{|}~-]",
                cardinality:1,
                casing:"lower"
            }
        }
    })
    $('#setVal').on('focus', function(event) {
        $(this).inputmask("setvalue", 'test@mail.com');
    });


});