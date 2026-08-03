const importStatusElement = document.querySelector('[data-code-import-status]');

if (importStatusElement) {
    const statusUrl = importStatusElement.dataset.statusUrl;
    const fallbackFailureMessage = importStatusElement.dataset.failureMessage;
    let pollingStopped = false;

    const stopWithError = (message) => {
        pollingStopped = true;
        importStatusElement.classList.remove('alert-info');
        importStatusElement.classList.add('alert-error');
        importStatusElement.setAttribute('role', 'alert');
        importStatusElement.textContent = message || fallbackFailureMessage;
    };

    const pollImportStatus = async () => {
        if (pollingStopped || !statusUrl) {
            return;
        }

        try {
            const response = await fetch(statusUrl, {
                headers: {
                    Accept: 'application/json',
                },
                cache: 'no-store',
                credentials: 'same-origin',
            });

            if (!response.ok) {
                throw new Error(fallbackFailureMessage);
            }

            const importStatus = await response.json();

            if (importStatus.status === 'completed') {
                pollingStopped = true;
                window.location.reload();

                return;
            }

            if (importStatus.status === 'failed') {
                stopWithError(importStatus.message);

                return;
            }

            importStatusElement.textContent = importStatus.message;
            window.setTimeout(pollImportStatus, 2500);
        } catch (error) {
            stopWithError(error instanceof Error ? error.message : fallbackFailureMessage);
        }
    };

    window.setTimeout(pollImportStatus, 2500);
}
