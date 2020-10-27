$(document).ready(function(e) {
   
   $('#alert-ok').hide();
   $('#alert-ko').hide();
   
   // Récupération de la liste des bundles pour le champ select
    getBundleList();
   
   // Gestion des bundles
   $('form[id=bundleForm]').submit(function(event){
      event.preventDefault();

      var bundleName = $( "#bundleName" ).val();

      if(bundleName !== ''){
        var url = 'bundle';
        var data = {bundleName: bundleName, action: 'create'};
        getAjax(url, data);

        // vide le input
        $( "#bundleName" ).val('');
      }
    });
    
    // Gestion des entités
    $('form[id=entityForm]').submit(function(event){
        event.preventDefault();
        
        var entityName = $( "#entityName" ).val();
        var listBundle = $( "#listBundle" ).val();
        var bddName = $( "#listBdd" ).val();
        
        if(entityName !== '' && listBundle !== null && bddName !== null ){
            
            var url = 'entity';
            var data = {entityName: entityName, listBundle:listBundle, bddName:bddName, action: 'create'};
            
            getAjax(url, data);
            
            // vide le input
            $( "#entityName" ).val('');
        } else {
            $('#msg-ko').html("Veuillez remplir tous les champs");
            $('#alert-ko').slideDown().delay(5000).slideUp();
        }
        
    });
    
    function getAjax (url, data){
        
      $.ajax({
        type: 'POST',
        url: './core/Manager/' + url + '.php', 
        data: data,
        dataType: "json",
        cache: false,             

        success: function(data)  
        {
          if(data.type === 'ok'){
              $('#msg-ok').html(data.msg);
              $('#alert-ok').slideDown().delay(7000).slideUp();

              // Récupération de la liste des bundles pour le champ select
              getBundleList();
          }

          if(data.type === 'ko'){
              $('#msg-ko').html(data.msg);
              $('#alert-ko').slideDown().delay(7000).slideUp();
          }

          if(data.type === 'listBundle'){
              $('#listBundle').html(data.list);
              $('#listBdd').html(data.bddKeys);
          }
        },
        error: function () {
            console.log("ERREUR : permission refusée");
        }
      });
    };
    
    // On récupère la liste des bundles
    function getBundleList(){
        var url = 'getOptions';
        var data = {val: ''};
        getAjax(url, data);
    } 
});