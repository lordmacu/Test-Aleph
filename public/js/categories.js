$(document).ready(function() {
    $('#import-btn').on('click', function() {
        console.log("Botón 'Importar' clickeado.");

        const importButton = $(this);
        const originalButtonText = importButton.html();
        const messageDiv = $('#ajax-message');

        importButton.prop('disabled', true).html(
            `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Importando...`
        );
        messageDiv.html('');

        $.ajax({
            url: importUrl,
            type: 'POST',
            dataType: 'json',
            data: data,
            success: function(response) {
                if (response.status === 'success') {
                    messageDiv.html(
                        `<div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>¡Éxito!</strong> ${response.message}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>`
                    );
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                } else {
                    showError(response.message || 'Ocurrió un error inesperado.');
                }
            },
            error: function(jqXHR) {
                const errorMessage = jqXHR.responseJSON ? jqXHR.responseJSON.message : 'Error de conexión con el servidor.';
                showError(errorMessage);
            },
            complete: function(jqXHR) {
                if (!jqXHR.responseJSON || jqXHR.responseJSON.status !== 'success') {
                    setTimeout(function() {
                        importButton.prop('disabled', false).html(originalButtonText);
                    }, 1000);
                }
            }
        });

        function showError(message) {
            messageDiv.html(
                `<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Error:</strong> ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>`
            );
        }
    });
});