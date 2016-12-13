<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/html">
  <head>
    <style>
      // Usefull CSS from BalusC http://stackoverflow.com/a/2913366
      .currencyinput
      {
          border: 1px inset #ccc;
      }
    </style>
    <link href="dropzone/dropzone.css" rel="stylesheet" />
    <link href="dropzone/min/basic.min.css" rel="stylesheet" />
    <script src="jquery.min.js"></script>
    <script src="dropzone/dropzone.js"></script>
    <script>
      // From official dropzone wiki: https://github.com/enyo/dropzone/wiki/Combine-normal-form-with-Dropzone cleaned up and customized
      // The camelized version of the ID of the form element
      Dropzone.options.productForm =
      {      
        // The configuration we've talked about above
        autoProcessQueue: false,
        uploadMultiple: false,
        parallelUploads: 1,
        maxFiles: 1,
        paramName: "img", // The name that will be used to transfer the file
      
        // The setting up of the dropzone
        init: function()
        {
          var myDropzone = this;
      
          // First change the button to actually tell Dropzone to process the queue.
          // Snippet from https://github.com/enyo/dropzone/issues/418 to send without any images uploaded,
          // IMPORTATNT DON'T reuse, it required changes to dropzone
          $('button[type="submit"]').on("click", function (e)
          {
            e.preventDefault();
            e.stopPropagation();

            var form = $(this).closest('#product-form');
            if (myDropzone.getQueuedFiles().length > 0)
              myDropzone.processQueue();
            else
              myDropzone.uploadFiles([]); //send empty
          });
      
          // Listen to the sendingmultiple event. In this case, it's the sendingmultiple event instead
          // of the sending event because uploadMultiple is set to true.
          this.on("sendingmultiple", function()
          {
            // Gets triggered when the form is actually being sent.
            // Hide the success button or the complete form.
          });
          
          this.on("success", function(files, response, e)
          {
            // Gets triggered when the files have successfully been sent.
            // Redirect user or notify of error.
            if (response === 'OK')
              window.location = '<?= strtok($_SERVER['REQUEST_URI'], '?') ?>';
            else
              alert('Error: ' + response);
          });
          
          this.on("error", function(files, response, e)
          {
            // Gets triggered when the files have encountered an error
            // Notify user of error
            alert('Error: ' + response);
          });
          
          this.on("errormultiple", function(files, response)
          {
            // Gets triggered when there was an error sending the files.
            // Maybe show form again, and notify user of error
          });
        },
      }
    </script>
  </head>
  <body>
    <form action='CreateProduct.php' id="product-form" class="dropzone">
      <legend>Add Product</legend>

        <!-- Title -->
        <div>
          <label for="t">Title*</label>
          <div>
            <input id="t" name="t" type="text" />
          </div>
        </div>
        
        <!-- Variant -->
        <br />
        <div>
          <label for="v">Variant*</label>
          <div>
            <input id="v" name="v" type="text" />
          </div>
        </div>
        
        <!-- Description -->
        <br />
        <div>
          <label for="d">Description*</label>
          <div>
            <textarea id="d" name="d" type="text"></textarea>
          </div>
        </div>
        
        <!-- Price -->
        <br />
        <div>
          <label for="p">Price*</label>
          <div>
            <span class="currencyinput">£<input type="text" id="p" name="p"></span>
          </div>
        </div>
        
        <!-- Type -->
        <br />
        <div>
          <label for="ty">Type*</label>
          <div>
            <input id="ty" name="ty" type="text" />
          </div>
        </div>
        
        <br />
        <!-- Image Upload -->
        <div id="keepSearchTogether">
          <div class="dz-default dz-message" style="height:100px; margin: 0px; border: 1px; border-style: dashed; vertical-align: middle; ">Drag image here to upload (Or click for traditional menu)</div>
          <div class="fallback">
            <input name="img" type="file" multiple />
          </div>
        </div>
        
        <!-- Stock Keeping Unit -->
        <br />
        <div>
          <label for="sku">Stock Keeping Unit (Unique ID)</label>
          <div>
            <input id="sku" name="sku" type="text" value='' />
          </div>
        </div>
        
        <button type="submit">Submit data and files!</button>
    </form>
  </body>
</html>