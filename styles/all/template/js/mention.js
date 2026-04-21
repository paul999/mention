$(document).ready(function () {
    var mentionThemeStyleId = 'mention-contrast-style';
    var mentionThemeObserverKey = '__mentionThemeObserver';

    function getReferenceElement() {
        return (
            document.querySelector('.dropdown-contents') ||
            document.querySelector('.dropdown') ||
            document.querySelector('.panel') ||
            document.querySelector('.forabg') ||
            document.querySelector('.post') ||
            document.body
        );
    }

    function luminance(rgb) {
        var matches = rgb.match(/\d+/g) || [0, 0, 0];
        var channels = matches.slice(0, 3).map(Number);

        while (channels.length < 3) {
            channels.push(0);
        }

        return channels.map(function (value) {
            value /= 255;
            return value <= 0.03928 ? value / 12.92 : Math.pow((value + 0.055) / 1.055, 2.4);
        }).reduce(function (accumulator, value, index) {
            if (index === 0) {
                return 0.2126 * value;
            }
            if (index === 1) {
                return accumulator + 0.7152 * value;
            }
            return accumulator + 0.0722 * value;
        }, 0);
    }

    function pickTextColor(backgroundColor) {
        return luminance(backgroundColor) > 0.45 ? '#2f3948' : '#d7dde6';
    }

    function pickHighlight(backgroundColor) {
        return luminance(backgroundColor) > 0.45 ? 'rgba(47, 57, 72, 0.10)' : 'rgba(255, 255, 255, 0.08)';
    }

    function applyMentionTheme() {
        var existingStyle = document.getElementById(mentionThemeStyleId);

        if (existingStyle) {
            existingStyle.remove();
        }

        var referenceElement = getReferenceElement();
        var styles = getComputedStyle(referenceElement);
        var textColor = pickTextColor(styles.backgroundColor);
        var highlightColor = pickHighlight(styles.backgroundColor);

        var style = document.createElement('style');
        style.id = mentionThemeStyleId;
        style.textContent = [
            '.tribute-container ul {',
            '    background-color: ' + styles.backgroundColor + ' !important;',
            '    color: ' + textColor + ' !important;',
            '}',
            '.tribute-container li {',
            '    color: ' + textColor + ' !important;',
            '    font-weight: 400 !important;',
            '}',
            '.tribute-container li span {',
            '    color: ' + textColor + ' !important;',
            '    font-weight: 400 !important;',
            '}',
            '.tribute-container li.highlight {',
            '    color: ' + textColor + ' !important;',
            '    background-color: ' + highlightColor + ' !important;',
            '}',
            '.tribute-container li.no-match {',
            '    color: ' + textColor + ' !important;',
            '    opacity: 0.98 !important;',
            '}'
        ].join('\n');
        document.head.appendChild(style);
    }

    function remoteSearch(query, callback) {
        if (query.length < MIN_MENTION_LENGTH) {
            callback([]);
            return;
        }
        $.getJSON(U_AJAX_MENTION_URL, {q: query}, function (data) {
            callback(data)
        });
    }

    if (window[mentionThemeObserverKey]) {
        window[mentionThemeObserverKey].disconnect();
    }

    applyMentionTheme();

    window[mentionThemeObserverKey] = new MutationObserver(function () {
        applyMentionTheme();
    });
    window[mentionThemeObserverKey].observe(document.documentElement, {
        attributes: true,
        attributeFilter: ['class', 'style']
    });
    window[mentionThemeObserverKey].observe(document.body, {
        attributes: true,
        attributeFilter: ['class', 'style']
    });

    tribute = new Tribute({
        noMatchTemplate: function () {
            return '<li>' + SIMPLE_MENTION_NO_MATCH + '</li>';
        },
        collection: [{
            trigger: '@',
            menuItemTemplate: function (item) {
                if (item.original.type === 'group') {
                    return item.original.value +  SIMPLE_MENTION_GROUP_NAME.replace('{CNT}', item.original.cnt) ;
                }
                return item.original.value;
            },

            selectTemplate: function (item) {
                if (!item || !item.original) {
                    return '';
                }

                if (item.original.type === 'user') {
                    return '[smention u=' + item.original.user_id + ']' + item.original.value + '[/smention]';
                }
                else if (item.original.type === 'group') {
                    return '[smention g=' + item.original.group_id + ']' + item.original.value + '[/smention]';
                }
            },
            values: remoteSearch,
            spaceSelectsMatch: true,
            lookup: 'value',
        }]
    });
    tribute.attach($('[name="message"]'));
});
