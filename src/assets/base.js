(function($) {
    $(function() {
        function isCorrect(input) {
            var testAnswer = $.trim(input.val().toLowerCase()).replace(
                /[\.'"â€™,-\/#!$%\^&\*;:{}=\-_`~()]/g,
                ""
            );
            var realAnswer = input
                .attr("__SINGLE-INPUT-DATA-ANSWER-ATTRIBUTE-NAME__")
                .toLowerCase()
                .replace(/[\.'"â€™,-\/#!$%\^&\*;:{}=\-_`~()]/g, "");
            return testAnswer == realAnswer;
        }

        function supportsLocalStorage() {
            return (
                !!window.localStorage &&
                typeof localStorage.getItem === "function" &&
                typeof localStorage.setItem === "function" &&
                typeof localStorage.removeItem === "function"
            );
        }

        function saveToLocalStorage() {
            var notesData = { blank: [], pnote: [] };
            $("input.blank").each(function(i) {
                notesData.blank[i] = $(this).val();
            });
            $("textarea.pnoteText").each(function(i) {
                notesData.pnote[i] = $(this).val();
            });
            localStorage.setItem("notesSlug", JSON.stringify(notesData));
        }

        function saveAsPdf() {
            var notes_text = $("form#notes").clone()[0];

            $(notes_text)
                .find(".blank")
                .each(function() {
                    answer = $(this).val();
                    $(this).replaceWith("<strong>" + answer + "</strong>");
                });
            $(notes_text)
                .find(".pnoteText")
                .each(function() {
                    answer = $(this).val();
                    $(this).replaceWith(
                        "<div style='white-space: pre-wrap;'><em>" +
                            answer +
                            "</em></div>"
                    );
                });

            var title = $("#notes-title").text();
            var today_pretty = new Date().toDateString();

            var mywindow = window.open("", "new div", "height=800,width=800");
            var mywindowHTML = "<html><head><title>Sermon Notes</title>";
            /* optional stylesheet */
            mywindowHTML += '<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css">';
            mywindowHTML += '</head><body><div class="container mt-3">';
            mywindowHTML += "<h1>" + title + "</h1>";
            mywindowHTML += "<h2>" + today_pretty + "</h2><hr>";
            mywindowHTML += $(notes_text).html();
            mywindowHTML += "</div></body></html>";

            mywindow.document.body.innerHTML = mywindowHTML;

            setTimeout(function() {
                mywindow.print();
                setTimeout(function() {
                    mywindow.close();
                }, 1000);
            }, 500);

            return true;
        }

        $("input.blank")
            .on("keyup", function() {
                // after every keystroke
                var input = $(this);
                var answer = input.val();
                if (
                    answer[answer.length - 1] == " " &&
                    input
                        .attr("__SINGLE-INPUT-DATA-ANSWER-ATTRIBUTE-NAME__")
                        .indexOf(" ") == -1
                ) {
                    // if last character was a space (and answer doesn't contain spaces), skip to next field on line
                    input.next().focus();
                }
                if (isCorrect(input)) {
                    input.addClass("__CORRECT_ANSWER_CLASS__");
                } else {
                    input.removeClass(
                        "__CORRECT_ANSWER_CLASS__ __WRONG_ANSWER_CLASS__"
                    );
                }
            })
            .on("blur", function() {
                // after the field is deselected
                var input = $(this);
                if (isCorrect(input)) {
                    input
                        .addClass("__CORRECT_ANSWER_CLASS__")
                        .val(
                            input.attr(
                                "__SINGLE-INPUT-DATA-ANSWER-ATTRIBUTE-NAME__"
                            )
                        );
                } else if (input.val().length) {
                    input.addClass("__WRONG_ANSWER_CLASS__");
                }
            })
            .on("focus", function() {
                // when the field is selected
                var input = $(this);
                if (input.hasClass("__WRONG_ANSWER_CLASS__"))
                    input.val("").removeClass("__WRONG_ANSWER_CLASS__");
            })
            .on("keypress", function(e) {
                var code = e.keyCode || e.which;
                if (code == 13) return false;
            });

        // auto-grow textarea with content
        $(".pnoteText")
            .on("keyup", function() {
                // reset the padding first (scrollHeight in Firefox works differently than other browsers)
                $(this).css("padding", "0");

                // measure the real scrollHeight
                $(this).css("height", "0"); // Reset the height
                $(this).css(
                    "height",
                    Math.min(this.scrollHeight + 15, 400) + "px"
                );

                // restore the padding
                $(this).css("padding", "");
            })
            .trigger("keyup");

        // auto-fill functionality
        $(".autofill").on("click", function() {
            if (
                !confirm(
                    "The best way to learn the message is by filling out the notes yourself. This function is only provided as a convienience.\n\nAre you sure you want to fill out these notes automatically?"
                )
            )
                return false;

            $("#notes input.blank")
                .each(function() {
                    $(this)
                        .addClass("__CORRECT_ANSWER_CLASS__")
                        .removeClass("__WRONG_ANSWER_CLASS__")
                        .val(
                            $(this).attr(
                                "__SINGLE-INPUT-DATA-ANSWER-ATTRIBUTE-NAME__"
                            )
                        );
                })
                .first()
                .trigger("change");
            saveToLocalStorage();
            return false;
        });

        $(".clearnotes").on("click", function() {
            if (
                !confirm(
                    "This will clear all blanks and personal notes. Are you sure you want to do this?"
                )
            )
                return false;
            $("#notes input.blank")
                .val("")
                .trigger("keyup");
            $(".pnoteText")
                .val("")
                .trigger("blur");
            localStorage.setItem("notesSlug", null);
            return false;
        });

        // handle the email button
        $("#emailButton").on("click", function(e) {
            $("#formAction").val("email");
            var email = prompt(
                "What email address would you like to send this to?"
            );
            if (!!email && email.length) {
                $("#formEmail").val(email);
            } else {
                e.preventDefault();
            }
        });

        // handle the download button
        $(".saveAsPdf").on("click", function() {
            saveAsPdf();
        });

        // save and restore data auto-magically
        if (supportsLocalStorage()) {
            // restore previously saved data if any exists
            var notesData;
            if ((notesData = localStorage.getItem("notesSlug"))) {
                var notesData = JSON.parse(notesData);
                if (typeof notesData == "object" && notesData != null) {
                    $("input.blank").each(function(i) {
                        if (
                            typeof notesData.blank[i] == "string" &&
                            !notesData.blank[i].length
                        )
                            return;

                        $(this).val(notesData.blank[i]);

                        if (isCorrect($(this)))
                            $(this).addClass("__CORRECT_ANSWER_CLASS__");
                        else $(this).addClass("__WRONG_ANSWER_CLASS__");
                    });
                    $("textarea.pnoteText").each(function(i) {
                        $(this).val(notesData.pnote[i]);
                        if (notesData.pnote[i].length) {
                            $(this)
                                .removeClass("hide")
                                .trigger("keydown");
                            $(this)
                                .prev()
                                .addClass("hide");
                        }
                        // reset the padding first (scrollHeight in Firefox works differently than other browsers)
                        $(this).css("padding", "0");

                        // measure the real scrollHeight
                        $(this).css("height", "0"); // Reset the height
                        $(this).css(
                            "height",
                            Math.min(this.scrollHeight + 15, 400) + "px"
                        );

                        // restore the padding
                        $(this).css("padding", "");
                    });
                }
            }

            // save data as inputs/textareas are changed
            $("input.blank, textarea.pnoteText").on("blur", function() {
                saveToLocalStorage();
            });
        }
    });
})(jQuery);
