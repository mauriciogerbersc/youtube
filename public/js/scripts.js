
function stringCount(haystack, needle) {
    if (!needle || !haystack) {
        return false;
    } else {
        count = {};
        $.each(haystack, function(k, v) {
            var words = v.split(needle);

            for (var i = 0, len = words.length; i < len; i++) {
                if (count.hasOwnProperty(words[i])) {
                    count[words[i]] = parseInt(count[words[i]], 10) + 1;
                } else {
                    count[words[i]] = 1;
                }
            }
        });
        return count;
    }
}