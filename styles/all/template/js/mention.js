$(document).ready(function () {
    function remoteSearch(query, callback) {
        if (query.length < MIN_MENTION_LENGTH) {
            callback([]);
            return;
        }
        $.getJSON(U_AJAX_MENTION_URL, {q: query}, function (data) {
            callback(data)
        });
    }

    tribute = new Tribute({
        collection: [{
            trigger: '@',
            menuItemTemplate: function (item) {
                return item.string;
            },

            selectTemplate: function (item) {
                return '[mention]' + item.original.value + '[/mention]';
            },
            values: remoteSearch,
            spaceSelectsMatch: true,
        }]
    });
    tribute.attach($('[name="message"]'));
});
