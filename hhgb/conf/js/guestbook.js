document.addEventListener('click', (event) => {
    const button = event.target.closest('button[data-hhgb-smiley]');
    const message = button?.form?.elements.namedItem('nachricht');
    if (!message) return;

    message.setRangeText(button.dataset.hhgbSmiley, message.selectionStart, message.selectionEnd, 'end');
    message.focus();
});
