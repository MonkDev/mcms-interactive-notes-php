<?php

require '../vendor/autoload.php';
use MonkDev\InteractiveNote\InteractiveNote;

$notes = new InteractiveNote(file_get_contents('note.txt'));
$notes->setSingleInputTemplate("<input name='single-line[]' class='blank form-control single-line' data-answer='__ANSWER__' type='text'>");
$notes->setFreeFormTemplate("<textarea name='free-form[]' class='pnoteText form-control free-form w-100' cols='30' rows='10' data-answer='__ANSWER__' placeholder='__ANSWER__'></textarea>");
$notes->setCorrectAnswerClass('is-valid');
$notes->setWrongAnswerClass('is-invalid');
$notes->disableAutoWidth();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Notes Testing</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
    <?= $notes->getCssSnippet(); ?>
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-md-8 offset-2 my-5">
            <h1 id="notes-title">Notes Test</h1>
            <div>
                <form id="notes" class="form-inline">
                    <?= $notes->parse(); ?>
                </form>
                <button href="#" class="autofill btn btn-outline-secondary">Fill in the answers for me</button>
                <button href="#" class="clearnotes btn btn-outline-secondary">Start Over</button>
                <button href="#" class="saveAsPdf btn btn-outline-secondary">Save as PDF</button>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.5.3/jspdf.debug.js" integrity="sha384-NaWTHo/8YCBYJ59830LTz/P4aQZK1sS0SneOgAvhsIl3zBu8r9RevNg5lHCHAuQ/" crossorigin="anonymous"></script>

<?= $notes->getJavascriptSnippet(); ?>
</body>
</html>
