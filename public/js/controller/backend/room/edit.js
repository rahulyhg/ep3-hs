(function() {

    $(document).ready(function() {

        if ($("#erf-pictures").find("img").length) {
            $("#erf-pictures").show().appendTo( $("#erf-picture").closest("td") );
        }

    });

})();