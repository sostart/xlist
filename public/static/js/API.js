var API = {

    url: '',

    token: '',

    get: function (path, params, callback) {
        API.ajax('GET', path, params, callback);
    },

    post: function (path, params, callback) {
        API.ajax('POST', path, params, callback);
    },

    ajax: function (method, path, params, callback) {
        params.token = API.token;
        $.ajax({
            method: method,
            url: API.url+path,
            data: params,
            dataType: 'json',
        }).done(callback);
    }
};
