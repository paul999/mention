$(document).ready(function() {
    $('[name="message"]').atwho({
        at: "@",
        insertTpl: '[mention]${name}[/mention]',
        callbacks: {
            /*
             It function is given, At.js will invoke it if local filter can not find any data
             @param query [String] matched query
             @param callback [Function] callback to render page.
             */
            remoteFilter: function(query, callback) {
                if (query.length < 3) {
                    callback([]);
                }
                else {
                    $.getJSON(U_AJAX_MENTION_URL, {q: query}, function (data) {
                        console.log(data);
                        callback(data)
                    });
                }
            },
            matcher: function(flag, subtext) {
                var regexp = new XRegExp('(\\s+|^)' + flag + '(\\p{L}+)$', 'gi');
                var match = regexp.exec(subtext);
                return match[2];
            }
        }
    });
});
