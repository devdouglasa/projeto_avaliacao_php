$(function () {
    $('.js-confirm-delete').on('submit', function (event) {
        if (!window.confirm('Deseja realmente excluir este serviço?')) {
            event.preventDefault();
        }
    });

    $('.js-confirm-finish').on('submit', function (event) {
        if (!window.confirm('Finalizar este serviço? A data será gravada, a comissão calculada e um e-mail enviado ao usuário.')) {
            event.preventDefault();
        }
    });

    $('.js-validate-login').on('submit', function (event) {
        var email = $.trim($(this).find('[name="email"]').val());
        var password = $(this).find('[name="password"]').val();

        if (!email || !password) {
            event.preventDefault();
            window.alert('Informe e-mail e senha.');
        }
    });

    $('.js-validate-register').on('submit', function (event) {
        var name = $.trim($(this).find('[name="name"]').val());
        var email = $.trim($(this).find('[name="email"]').val());
        var password = $(this).find('[name="password"]').val();

        if (!name || !email || !password) {
            event.preventDefault();
            window.alert('Preencha nome, e-mail e senha.');
        }
    });

    $('.js-validate-service').on('submit', function (event) {
        var description = $.trim($(this).find('[name="description"]').val());
        var price = $.trim($(this).find('[name="price"]').val());

        if (!description || !price) {
            event.preventDefault();
            window.alert('Informe a descrição e o valor do serviço.');
        }
    });

    $('.js-price').on('blur', function () {
        var raw = $(this).val().replace(/[^\d,.]/g, '');
        if (!raw) {
            return;
        }

        raw = raw.replace(/\./g, '').replace(',', '.');
        var number = parseFloat(raw);

        if (!isNaN(number)) {
            $(this).val(number.toFixed(2).replace('.', ','));
        }
    });
});
