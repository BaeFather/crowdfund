var MsgBox = {
    /* Alert */
    Alert: function(type, msg, okhandler) {
        new Promise((resolve, reject) => {
            var typeId = $('#alert-suc');
            var typeMsg = $("#alert-suc .alert-msg");
            var typeButton = $("#alert-suc .alert-ok");

            if(type == 'info') {
                typeId = $('#alert-info');
                typeMsg = $("#alert-info .alert-msg");
                typeButton = $("#alert-info .alert-ok");
            } else if(type == 'warn') {
                typeId = $('#alert-warning');
                typeMsg = $("#alert-warning .alert-msg");
                typeButton = $("#alert-warning .alert-ok");
            } else if(type == 'error') {
                typeId = $('#alert-error');
                typeMsg = $("#alert-error .alert-msg");
                typeButton = $("#alert-error .alert-ok");
            }

            typeButton.unbind();
            typeMsg.html(msg);
            typeId.modal('show');

            typeButton.click(function() {
            	typeId.modal('hide');
            });

            typeId.on("hidden.bs.modal", function(e) {
                e.stopPropagation();
                if(okhandler != null) {
                    resolve();
                }
                else reject();
            });
        }).then(okhandler).catch(function() {});
    },
    /* Confirm */
    Confirm: function(msg, yeshandler, nohandler) {
        new Promise((resolve, reject) => {
            var flag = false;
            $("#alert-confirm #confirm-yes").unbind();
            $("#alert-confirm #confirm-no").unbind();
            $("#alert-confirm .alert-msg").html(msg);
            $('#alert-confirm').modal('show');

            $('#alert-confirm').on('keypress', function (e) {
                var keycode = (e.keyCode ? e.keyCode : e.which);
                if(keycode == '13') {
                    flag = true;
                    $('#alert-confirm').modal('hide');
                }
            });

            $("#alert-confirm #confirm-yes").click(function() {
                flag = true;
            });
            $("#alert-confirm #confirm-no").click(function() {
                flag = false;
            });

            $("#alert-confirm").on("hidden.bs.modal", function(e) {
                e.stopPropagation();
                if(yeshandler != null && flag == true) resolve(1);
                else if(nohandler != null && flag == false) resolve(2);
                else reject();
            });

        }).then(function(value) {
            if(value == 1)      yeshandler();
            else if(value == 2) nohandler();
        }).catch(function() {});
    },
}