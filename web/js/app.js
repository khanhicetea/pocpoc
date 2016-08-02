var vue = new Vue({
    el: '#app',
    data: {
        notifications: []
    },
});

db.allDocs({
    include_docs: true,
    limit: 50,
    descending: true
}).then(function (result) {
    result.rows.map(function(obj) {
        vue.notifications.push(obj.doc);
    });
}).catch(function (err) {
    console.log(err);
});

var sync = PouchDB.sync('notifications', remoteCouch, {
  live: true,
  retry: true
}).on('change', function (info) {
    if (info.direction == "pull" && info.change.ok) {
        info.change.docs.map(function(obj) {
            vue.notifications.unshift(obj);
            Push.create('PocPoc', {
                body: obj.message,
                icon: '/images/announcer.png',
                timeout: 3000,
                onClick: function () {
                    window.focus();
                    this.close();
                },
            });
        });
    }
}).on('denied', function (err) {
    console.log(err);
}).on('error', function (err) {
    console.log(err);
});

