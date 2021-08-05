# Interactive Notes

A PHP Library to transform HTML into an interactive fill the in blank style form.

### Installation into an existing MCMS Site

#### File install:
1. Copy the entire contents of `src/` to a new folder in the site: `_components/interactive_notes/` (has the class and `assets` folder)

#### Implementation into site:

1. In the `mcms_*` page where you'll be implementing interactive notes, make sure you've got the sermon/livestream detail with the `interactivenote` content.  Use the following code for ideas:
    ```php
    $interactive_note = getContent(
        "sermon",
        "display:auto",
        "show_detail:__interactivenote__",
        "noecho"
    );

    require_once($_SERVER["DOCUMENT_ROOT"] . "/_components/interactive_notes/InteractiveNote.php");
    $notes = new MonkDev\InteractiveNote\InteractiveNote(file_get_contents($interactive_note));

    if( !empty($interactive_note) ) {
        $notes->setSingleInputTemplate("<input name='single-line[]' class='blank form-control single-line' data-answer='__ANSWER__' type='text'>");
        $notes->setFreeFormTemplate("<textarea name='free-form[]' class='pnoteText form-control free-form w-100' cols='30' rows='10' data-answer='__ANSWER__' placeholder='__ANSWER__'></textarea>");
        $notes->setCorrectAnswerClass('is-valid');
        $notes->setWrongAnswerClass('is-invalid');
        $notes->disableAutoWidth();
    }
    ```
1. Get the notes into the page. Use something like the following, matching the page's style, but leave the `autofill`, `clearnotes`, and `saveAsPdf` classes on the buttons:
    ```html
    <?php if( !empty($notes->parse() ) : ?>
    <div class="row">
        <div class="col-md-8 offset-2 my-5">
            <h1 id="notes-title">Notes</h1>
            <div>
                <form id="notes" class="form-inline">
                    <?= $notes->parse(); ?>
                </form>
                <button href="javascript:void(0);" class="autofill btn btn-outline-secondary">Fill in the answers for me</button>
                <button href="javascript:void(0);" class="clearnotes btn btn-outline-secondary">Start Over</button>
                <button href="javascript:void(0);" class="saveAsPdf btn btn-outline-secondary">Save as PDF</button>
            </div>
        </div>
    </div>
    <?php endif; ?>
    ```
1. Somewhere after the `_components/scripts.php` include, you'll need to add the following to make the UI work:
    ```html
    <?= $notes->getCssSnippet(); ?>
    <?= $notes->getJavascriptSnippet(); ?>
    ```
