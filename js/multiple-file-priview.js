$(document).ready(function () {
    console.log("upload file preview js");
  
    let documentInput = $("#documents");
    let previewContainer = $("#imagePreviewContainer");
  
    // Create a DataTransfer object to hold the files
    const dataTransfer = new DataTransfer();
  
    documentInput.on("change", function (e) {
      // Convert FileList to array
      const files = Array.from(e.target.files);
  
      files.forEach((file) => {
        const reader = new FileReader();
  
        // Create a unique ID for each preview card
        const uniqueId = "preview_" + Math.random().toString(36).substr(2, 9);
  
        // FileReader onload event - execute after the file is read
        reader.onload = function (e) {
          const previewCard = `
              <div id="${uniqueId}" class="position-relative">
                <img src="${e.target.result}" alt="${file.name}"  class="img-thumbnail" style="width: 150px; height: 150px;" />
                <button data-filename="${file.name}" class="btn btn-danger btn-sm position-absolute remove-btn" style="top: 5px; right: 5px;">X</button>
              </div>
            `;
          
          // Add the file to DataTransfer object after reading
          dataTransfer.items.add(file);
          previewContainer.append(previewCard);
          
          // Update the file input with the new DataTransfer object
          documentInput[0].files = dataTransfer.files;
        };
  
        // Read the file as a data URL (base64)
        reader.readAsDataURL(file);
      });
  
      // Clear the original input to allow re-upload of the same file
      documentInput.val("");
      console.log("before delete", dataTransfer);
    });
  
    // Handle remove button click (delete preview and file from input)
    $(document).on("click", ".remove-btn", function () {
      const fileName = $(this).data("filename");
      console.log("filename", fileName);
  
      // Create a new DataTransfer object
      const newDataTransfer = new DataTransfer();
  
      $(this).closest("div").remove(); // Removes the parent div (preview card)
      console.log("after delete", dataTransfer);
  
      // Loop through current files and add them back except the deleted one
      Array.from(dataTransfer.files).forEach((file) => {
        if (file.name !== fileName) {
          newDataTransfer.items.add(file);
        }
      });
  
      console.log("new data ", newDataTransfer);
  
      // Replace the old DataTransfer with the new one
      dataTransfer.items.clear();
      Array.from(newDataTransfer.files).forEach((file) => {
        dataTransfer.items.add(file);
      });
  
      console.log("new datatransfer ", dataTransfer);
  
      // Update the file input after modification
      documentInput[0].files = dataTransfer.files;
  
      console.log("updated files", documentInput[0].files);
    });
  });
  