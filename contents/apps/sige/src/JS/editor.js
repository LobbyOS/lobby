$(document).ready(function(){
  tinymce.init({
    selector: 'textarea[name=content]',
    plugins: [
      "advlist autolink autoresize lists link image charmap print preview anchor",
      "searchreplace visualblocks code fullscreen",
      "insertdatetime media table contextmenu paste"
    ],
    toolbar: "insertfile undo redo | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent",
    init_instance_callback: function(){
      $(tinyMCE.DOM.get("content" + "_ifr").contentDocument).find("body").css("padding-bottom", "0px");
    }
  });
});
