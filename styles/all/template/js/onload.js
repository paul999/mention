/**
 *
 * Original file/functions from/by:
 * https://raw.githubusercontent.com/Wolfsblvt/mentions/master/styles/all/template/js/onload.js
 *
 * Modified by Paul Sohier to function with simply mention.
 * '@Mention System
 *
 * @copyright (c) 2015 Wolfsblvt ( www.pinkes-forum.de )
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 * @author Clemens Husung (Wolfsblvt)
 */

// Autocomplete feature
$(document).ready(function () {
    var $message = $("#message");

    var default_options = {
        data: $.paul999_mention.mentions_json_data.ajax_path,
        searchKey: ['@'],
        limit: 5,
        displayTpl: '<li><div class="mentions-avatar">${avatar}</div> ${username_no_profile}</li>',

        maxLen: 30,
        startWithSpace: true,
        displayTimeout: 300,
        highlightFirst: true,
        delay: null,
        suffix: '',
        hideWithoutSuffix: true,
    };

    // Unset data if we want to load it remove, and not at start
    // Then add the remote filter
    default_options.data = undefined;
    default_options.remoteFilter = $.paul999_mention.callback_remoteFilter;


    // Add the mention listeners
    $message.atwho($.extend({}, default_options, {
        at: "@",
        alias: "at",
        insertTpl: '${atwho-at}${name}',

        callbacks: {
            matcher: $.paul999_mention.callback_matcher_at,
            highlighter: $.paul999_mention.callback_highlighter,
            beforeSave: $.paul999_mention.callback_beforeSave,
            sorter: $.paul999_mention.callback_sorter,
            tplEval: $.paul999_mention.callback_tplEval,
        },
    }));

    $message.atwho($.extend({}, default_options, {
        at: "[mention]",
        closingTag: "[/mention]",
        alias: "mention-bbcode",
        insertTpl: '${atwho-at}${name}',

        callbacks: {
            matcher: $.paul999_mention.callback_matcher_mention,
            highlighter: $.paul999_mention.callback_highlighter,
            beforeSave: $.paul999_mention.callback_beforeSave,
            sorter: $.paul999_mention.callback_sorter,
            tplEval: $.paul999_mention.callback_tplEval,
        },
    }));

    $message.atwho($.extend({}, default_options, {
        at: "[mention]",
        prependingCode: "",
        closingTag: "[/mention]",
        closingTagTextInside: true,
        alias: "mention-equals-bbcode",
        insertTpl: '${name}', // dunno why the hell I can't use ${atwho-at} here, but it inserts two "[" at front. So let's come around with this

        callbacks: {
            matcher: $.paul999_mention.callback_matcher_mention,
            highlighter: $.paul999_mention.callback_highlighter,
            beforeSave: $.paul999_mention.callback_beforeSave,
            sorter: $.paul999_mention.callback_sorter,
            tplEval: $.paul999_mention.callback_tplEval,
        }
    }));


    $message.on("inserted-mention-bbcode.atwho", function (event, $li) {
        console.log(event, "inserted ", $li, "context is", this);
        $.paul999_mention.move_behind_tag($message);
    });
    $message.on("inserted-mention-equals-bbcode.atwho", function (event, $li) {
        console.log(event, "inserted ", $li, "context is", this);
        $.paul999_mention.move_behind_tag($message, true);
    });

    // Open autocomplete if bbcode is inserted
    $("input.bbcode-mention, input.bbcode-mention-text").on("click", function () {
        $message.atwho("run");
    });
});