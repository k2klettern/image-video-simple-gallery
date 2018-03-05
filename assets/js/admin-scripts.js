function showImage(src, target) {
    var fr=new FileReader();
    fr.onload = function(e) { target.src = this.result; };
    fr.readAsDataURL(src.files[0]);
}

jQuery(document).ready(function ($) {
    $(".draggable > li").draggable({
        revert: "invalid",
        cursor: "move",
        containment: "document",
        scroll: false,
        helper: "clone"
    });

    $(".droppable").droppable({
        accept: ".draggable > li",
        activeClass: "ui-state-highlight",
        drop: function (event, ui) {
            //ui.detach().appendTo($(this));
            var dataarray = $("ul.accordion.draggable").attr('data-array');
            var idplus = parseFloat(dataarray) + 1;
            var row = ui.draggable.clone(true);
                $(row).find('a.toggle').removeClass('notthis');
                $(row).find('input.hidenfield').attr('name', 'gallery[' + dataarray + '][type]');
                $(row).find('input.newtitle').attr('name', 'gallery[' + dataarray + '][title]');
                $(row).find("option").each( function () {
                    $(this).prop('selected', false);
                    $(this).parent('select.galleryid').attr('name', 'gallery[' + dataarray + '][id]');
                    $(this).parent('select.tagselector').attr('name', 'gallery[' + dataarray + '][tags][]');
                });
                var text = $(row).find('a.toggle').html();
                $(row).find('a.toggle').html(dataarray + " - " + text);
                row.appendTo($(this));
                $("ul.accordion.draggable").attr('data-array', idplus);
        }
    });


    $('.toggle').on( 'click', function(e) {
        e.preventDefault();
        if($(this).hasClass('notthis')) {
            return false;
        }
        var $this = $(this);

        if ($this.next().hasClass('show')) {
            $this.next().removeClass('show');
            $this.next().slideUp(350);
        } else {
            $this.parent().parent().find('li .inner').removeClass('show');
            $this.parent().parent().find('li .inner').slideUp(350);
            $this.next().toggleClass('show');
            $this.next().slideToggle(350);
        }
    });

    function handleDropEvent( event, ui ) {
        var draggable = ui.draggable;
        alert( 'The square with ID "' + draggable.attr('id') + '" was dropped onto me!' );
    }

    var fixHelperModified = function(e, tr) {
            var $originals = tr.children();
            var $helper = tr.clone();
            $helper.children().each(function(index) {
                $(this).width($originals.eq(index).width())
            });
            return $helper;
        },
        updateIndex = function(e, ui) {
            $('td.index', ui.item.parent()).each(function (i) {
                $(this).html(i + 1);
            });
        };

    $("#list-sort tbody").sortable({
        helper: fixHelperModified,
        stop: updateIndex
    }).disableSelection();

    $('.dashicons-plus-alt').on('click', function () {
            var dataarray = $(".clone-field").attr('data-clone');
                var idclon = dataarray;
                var idplus = parseFloat(idclon) + 1;
                var row = $("tr.img-clone").clone(true);
                $(row).find("tr.img-clone").removeClass('img-clone');
                $(row).find("td.index").html(parseFloat(idplus) + 1);
                $(row).find("input[type=text]").val('').change();
                $(row).find("#picsrc_0").attr('src', '');
                $(row).find("input[type=text]").each( function () {
                    $(this).attr("name", $(this, row).attr("name").replace(/\d+/, idplus));
                    var tid = $(this).attr('id').replace(/\d+/, "");
                    $(this).attr('id', tid + idplus).parent('div');
                });
                $(row).find("input[type=checkbox]").each( function () {
                    $(this).removeAttr('checked');
                    $(this).attr("name", $(this, row).attr("name").replace(/\d+/, idplus));
                    var tid = $(this).attr('id').replace(/\d+/, "");
                    $(this).parent().attr('id', tid + idplus).parent('div');
                });
        $(row).find("option").each( function () {
            $(this).prop('selected', false);
            $(this).parent('select').attr("name", $(this, row).parent().attr("name").replace(/\d+/, idplus));
            var tid = $(this).parent().attr('id').replace(/\d+/, "");
            $(this).parent().attr('id', tid + idplus).parent('div');
        });
                var rep = new RegExp(eval("/_0/g"));
                var xscript = $(row).find("script").html().replace(rep, "_" + idplus);
                $(row).find("script").replaceWith('<script>' + xscript + '</script>');
                // $(row).find("input[type=number]").val('').change();
                $(row).find("input[type=button]").each( function () {
                     var tid=$(this).attr('id').replace(/\d+/, "");
                     $(this).attr('id', tid + idplus);
                });
                $(row).find("#picsrc_0").each(function () {
                    var imid = $(this).attr('id').replace(/\d+/, "");
                    $(this).attr('class').replace(/\d+/, "");
                    $(this).attr("id", imid + idplus);
                    $(this).attr("class", imid + idplus);
                });
                $(row).removeClass('img-clone');
                $('tbody').append(row);
                dataarray++;
                $(".clone-field").attr('data-clone', dataarray);
    });

    $('a.delete-this').on('click', function(e) {
        e.preventDefault();
        var ui = $(this).closest('li');
            ui.remove();
    });

    function bs_input_file() {
        $(".input-file").before(
            function() {
                if ( ! $(this).prev().hasClass('input-ghost') ) {
                    var element = $("<input type='file' class='input-ghost' style='visibility:hidden; height:0'>");
                    element.attr("name",$(this).attr("name"));
                    element.change(function(){
                        element.next(element).find('input').val((element.val()).split('\\').pop());
                    });
                    $(this).find("button.btn-choose").click(function(){
                        element.click();
                    });
                    $(this).find("button.btn-reset").click(function(){
                        element.val(null);
                        $(this).parents(".input-file").find('input').val('');
                    });
                    $(this).find('input').css("cursor","pointer");
                    $(this).find('input').mousedown(function() {
                        $(this).parents('.input-file').prev().click();
                        return false;
                    });
                    return element;
                }
            }
        );
    }

    bs_input_file();


});