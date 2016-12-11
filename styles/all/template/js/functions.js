/**
 *
 * Original file/functions from/by:
 * https://raw.githubusercontent.com/Wolfsblvt/mentions/master/styles/all/template/js/functions.js
 *
 * Modified by Paul Sohier to function with simply mention.
 *
 * '@Mention System
 *
 * @copyright (c) 2015 Wolfsblvt ( www.pinkes-forum.de )
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 * @author Clemens Husung (Wolfsblvt)
 */

// namespacing
$.paul999_mention = $.extend({}, $.paul999_mention, {

    /// <field name="mentions_json_data" type="array">A list of data retrieved from the server that can be used for mentions.</field>
    mentions_json_data: paul999_mentions_json_data,

    /// <field name="mentions_username_data" type="array">A list of all usernames that can be mentioned.</field>
    mentions_username_data: [],

    callback_matcher_mention: function (flag, subtext, should_startWithSpace) {
        /// <summary>
        ///		Callback function that can be used in at-who for the matcher callback to match usernames.
        ///
        ///		This callback checks if there is any match in the data list of all usernames for the current given search word.
        /// </summary>
        /// <param name="flag">The flag to search for</param>
        /// <param name="subtext">The text from beginning until the current caret position.</param>
        /// <param name="should_startWithSpace">Bool if the flag should start with a space.</param>
        /// <returns type="string|null">Either the whole matched string, or null if no match.</returns>
        var match = $.paul999_mention.callback_matcher_at.call(this, flag, subtext, should_startWithSpace);


        // Close Tag automatically
        if (match === "") {
            var $inputor = this.$inputor;
            var currentPos = $inputor.caret('pos', { iframe: this.app.iframe });
            var prependingCode = (this.setting.prependingCode) ? this.setting.prependingCode : '';

            var parts = [
                $inputor.val().substring(0, currentPos),
                $inputor.val().substring(currentPos)
            ];

            // If mention tag isn't already closed in that line, we need to do that
            var current_line = parts[1].split('\n')[0];
            var closingTag_found = (current_line.indexOf(this.setting.closingTag) >= 0);

            // If we have some bbcodes closed inside, we should also allow text between it.
            if (!closingTag_found && this.setting.closingTagTextInside) {
                var real_closing_tag = this.setting.closingTag.replace(/^[^\]]\]/, ''); // cut the rest of the opening tag, cause we can't make sure that it is completed for the search
                var regex = $.paul999_mention.escape_for_regex(real_closing_tag);
                closingTag_found = (new RegExp(regex, 'gi')).test(current_line);
            }
            if (!closingTag_found) {
                $inputor.val(parts.join(prependingCode + this.setting.closingTag));
                $inputor.caret('pos', currentPos + prependingCode.length, {
                    iframe: this.app.iframe
                });
                if (!$inputor.is(':focus')) {
                    $inputor.focus();
                }
                $inputor.change();
            }

        }

        return match;
    },
    callback_matcher_at: function (flag, subtext, should_startWithSpace) {
        /// <summary>
        ///		Callback function that can be used in at-who for the matcher callback to match usernames.
        ///
        ///		This callback checks if there is any match in the data list of all usernames for the current given search word.
        /// </summary>
        /// <param name="flag">The flag to search for</param>
        /// <param name="subtext">The text from beginning until the current caret position.</param>
        /// <param name="should_startWithSpace">Bool if the flag should start with a space.</param>
        /// <returns type="string|null">Either the whole matched string, or null if no match.</returns>
        flag = $.paul999_mention.escape_for_regex(flag);
        if (should_startWithSpace) {
            flag = '(?:^|\\s)' + flag;
        }
        if (this.setting.prependingCode) {
            flag = flag + '(?:' + $.paul999_mention.escape_for_regex(this.setting.prependingCode) + ')?';
        }

        var regexp = new RegExp(flag + "(.*)$", 'gi');

        // Loop through all matches and see if this may be a mention
        while (match = regexp.exec(subtext)) {
            console.log(match);

            // If we have no data, we expect it is a match and wait for the data
            if ($.paul999_mention.mentions_username_data.length == 0) {
                return match[1];
            }

            // Check if there is at least one name matching the current mention expression
            for (var _i = 0, _len = $.paul999_mention.mentions_username_data.length; _i < _len; _i++) {
                var item = $.paul999_mention.mentions_username_data[_i];
                if (~new String(item[this.setting.searchKey]).toLowerCase().indexOf(match[1].toLowerCase())) {
                    return match[1];
                }
            }

            // We want to find ALL matches, so start from the next char
            regexp.lastIndex = match.index + 1;
        }

        // If nothing has matched, we return null. There is no possible mention
        return null;
    },
    callback_highlighter: function (li, query) {
        /// <summary>
        ///		Callback function that can be used in at-who for the highlighter callback to highlight usernames.
        ///
        ///		This callback highlights the given string that is searched for inside list elements that are shown.
        /// </summary>
        /// <param name="li">The list element that should be modified</param>
        /// <param name="query">The string that is searched for</param>
        /// <returns type="string">The modified html element of "li"</returns>
        var regexp;
        if (!query) {
            return li;
        }
        query = ($.paul999_mention.escape_for_regex(query))
            .replace("+", "\\+");
        regexp = new RegExp('<span([^<^>]*?)username([^<^>]*?)>\\s*(.*?)(' + query + ')(.*)\\s*<\/span>', 'ig');

        return li.replace(regexp, function (str, $1, $2, $3, $4, $5) {
            return '<span' + $1 + 'username' + $2 + '>' + $3 + '<strong>' + $4 + '</strong>' + $5 + '</span>';
        });
    },
    callback_beforeSave: function (data) {
        /// <summary>
        ///		Callback function that can be used in at-who for the beforeSave callback to modify or save the data.
        ///
        ///		This callback saves the data in global $.paul999_mention object so that it is accessible from other callbacks.
        /// </summary>
        /// <param name="data">The original data</param>
        /// <returns type="array">The data array</returns>
        var item, _i, _len, _results;
        if (!$.isArray(data)) {
            if ($.paul999_mention.mentions_username_data.length == 0) {
                $.paul999_mention.mentions_username_data = data;
            }
            return $.paul999_mention.callback_sorter.call(this, "", data, $.paul999_mention.mentions_json_data.searchKey);
        }
        _results = [];
        for (_i = 0, _len = data.length; _i < _len; _i++) {
            item = data[_i];
            if ($.isPlainObject(item)) {
                _results.push(item);
            } else {
                _results.push({
                    name: item
                });
            }
        }
        if ($.paul999_mention.mentions_username_data.length == 0) {
            $.paul999_mention.mentions_username_data = data;
        }
        return $.paul999_mention.callback_sorter.call(this, "", _results, $.paul999_mention.mentions_json_data.searchKey);
    },
    callback_sorter: function (query, items, searchKey) {
        /// <summary>
        ///		Callback function that can be used in at-who for the sorter callback to modify sorting order.
        ///
        ///		This callback changes the default sorting order of the sorter.
        /// </summary>
        /// <param name="query">The matching string</param>
        /// <param name="items">Array of data that was refactored</param>
        /// <param name="searchKey">Name of the property to search in the items</param>
        /// <returns type="array">The sorted items</returns>
        var item, _i, _len, _results;

        _results = [];
        for (_i = 0, _len = items.length; _i < _len; _i++) {
            item = items[_i];

            item.atwho_order = new String(item[searchKey]).toLowerCase().indexOf(query.toLowerCase());

            // Get current position in thread if has posted. Set to 0 (not in thread) if threadposter sorting is deactivated
            if ($.paul999_mention.mentions_json_data.autocomplete_topic_posters) {
                item.in_thread = $.inArray(item['user_id'], $.paul999_mention.mentions_json_data.poster_ids);
            }
            else {
                item.in_thread = 0;
            }

            if (item.atwho_order > -1) {
                _results.push(item);
            }
        }

        return _results.sort(function (a, b) {
            // If the query match is at the same position, we priorize the names that have already posted in this topic
            var difference = 0;
            if (a.atwho_order == b.atwho_order && (~a.in_thread || ~b.in_thread)) {
                difference = b.in_thread - a.in_thread; // Sort higher in_thread before
            } else {
                difference = a.atwho_order - b.atwho_order; // Sort lower atwho_order before
            }

            // If difference is 0 le'ts check the next letters
            if (difference == 0) {
                var text_a = a[searchKey].substring(a.atwho_order + 1).toLowerCase();
                var text_b = b[searchKey].substring(b.atwho_order + 1).toLowerCase();

                return text_a.localeCompare(text_b); // Compare texts from mathing position and sort alphabetically
            }
            return difference;
        });
    },
    callback_tplEval: function (tpl, map) {
        /// <summary>
        ///		Callback function that can be used in at-who for the tplEval callback to modify template of the list item.
        ///
        ///		This callback changes the default template for each list item.
        /// </summary>
        /// <param name="tpl">The orignal template string</param>
        /// <param name="map">the data for this specific item</param>
        /// <returns type="string">The template for the list item</returns>
        var error;
        try {
            var template = tpl.replace(/\$\{([^\}]*)\}/g, function (tag, key, pos) {
                return map[key];
            });

            return template;
        } catch (_error) {
            error = _error;
            return "";
        }
    },
    callback_remoteFilter: function (query, render_view) {
        /// <summary>
        ///		Callback function that can be used in at-who for the remoteFilter callback to load data manually.
        ///
        ///		This callback loads the userdata directly from the server.
        /// </summary>
        /// <param name="query">The string that is searched for.</param>
        /// <param name="render_view">Callback to render page.</param>
        /// <returns type="">nothing</returns>

        var thisVal = query,
            self = $(this);
        if (!self.data('active') && thisVal.length >= 2) {
            self.data('active', true);
            var itemsMentions = wolfsblvtCachequeryMentions[thisVal];
            if (typeof itemsMentions == "object") {
                $.paul999_mention.mentions_username_data = itemsMentions;
                render_view(itemsMentions);
            } else {
                if (self.xhr) {
                    self.xhr.abort();
                }
                self.xhr = $.getJSON($.paul999_mention.mentions_json_data.ajax_path, {
                    term: thisVal
                }, function (data) {
                    $.paul999_mention.mentions_username_data = data;
                    render_view(data);
                });
            }
            self.data('active', false);
        }
    },

    move_behind_tag: function (inputor, inside) {
        /// <summary>
        ///		Moves the cursor from the current position behind the tag if there is one.
        ///		It it can be a following tag or the one we are currently inside, if specified.
        /// </summary>
        /// <param name="inputor">The inputor where the text is inserted.</param>
        /// <param name="inside">Bool specifying if we are currently inside the tag. If not, it is expected that it will directly follow.</param>
        /// <returns type="bool">True if moved, else false.</returns>
        var $inputor = $(inputor);
        var currentPos = $inputor.caret('pos', { iframe: window.frameElement });

        var parts = [
            $inputor.val().substring(0, currentPos),
            $inputor.val().substring(currentPos)
        ];

        var regex = (inside) ? /^([^\]]*\]).*$/gi : /^(\[[^\]]*\]).*$/gi;

        // If first first thing after caret is a bbcode, we move behind it
        if (match = regex.exec(parts[1].split('\n')[0])) {
            $inputor.caret('pos', currentPos + match[1].length, {
                iframe: window.frameElement
            });
            if (!$inputor.is(':focus')) {
                $inputor.focus();
            }
            return true;
        }
        return false;
    },

    escape_for_regex: function (string) {
        /// <summary>
        ///		Escapes chars in a given string so that it can be used in a regex.
        /// </summary>
        /// <param name="string">The string wich should be replaced.</param>
        /// <returns type="string">Escaped string.</returns>
        var escaped = string.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, "\\$&")
        return escaped;
    },
});