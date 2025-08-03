$(function() {
  $('.image-upload-preview').each(function() {
    const previewContainer = $(this);
    const inputId = previewContainer.data('target-input');
    const fileInput = $('#' + inputId);
    const previewImage = previewContainer.find('.preview-img');
    const dragDropOverlay = previewContainer.find('.drag-drop-overlay');

    // Click on preview triggers file input
    previewContainer.on('click', function() {
      fileInput.trigger('click');
    });

    // When file input changes, update preview
    fileInput.on('change', function(e) {
      const file = e.target.files[0];
      if (file && file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = function(e) {
          previewImage.attr('src', e.target.result);
        };
        reader.readAsDataURL(file);
      }
    });

    // Drag & drop events
    previewContainer.on('dragenter dragover', function(e) {
      e.preventDefault();
      e.stopPropagation();
      dragDropOverlay.show();
      previewContainer.css('border-color', '#007bff');
    });

    previewContainer.on('dragleave drop', function(e) {
      e.preventDefault();
      e.stopPropagation();
      dragDropOverlay.hide();
      previewContainer.css('border-color', '#ccc');
    });

    previewContainer.on('drop', function(e) {
      const files = e.originalEvent.dataTransfer.files;
      if (files.length > 0) {
        const file = files[0];
        if (file.type.startsWith('image/')) {
          // Update file input files property
          const dataTransfer = new DataTransfer();
          dataTransfer.items.add(file);
          fileInput[0].files = dataTransfer.files;

          // Update preview image
          const reader = new FileReader();
          reader.onload = function(e) {
            previewImage.attr('src', e.target.result);
          };
          reader.readAsDataURL(file);
        }
      }
    });
  });
});
