Mautic.recommenderOnLoad = function (container, response) {

}
mQuery('.recommender-preview .editor-basic').on('froalaEditor.contentChanged', function(){
        Mautic.recommenderUpdatePreview();
});

mQuery(document).on('blur', '.recommender-preview input:text', function(){
    Mautic.recommenderUpdatePreview();
});

mQuery(document).on('change', '.recommender-preview select', function(){
    Mautic.recommenderUpdatePreview();
});

mQuery(document).on('change', '.recommender-preview input:radio', function(){
    Mautic.recommenderUpdatePreview();
});

Mautic.recommenderUpdatePreview = function () {
    mQuery('#recommender-preview').fadeTo('normal', 0.4);
    mQuery('#recommender-preview-loader').show();
    var data = mQuery('form[name=recommender]').formToArray();
    Mautic.ajaxActionRequest('plugin:recommender:generatePreview', data, function (response) {
        if(response.content) {
            mQuery('#recommender-preview').html(response.content);
        }
        mQuery('#recommender-preview').fadeTo('normal', 1);
        mQuery('#recommender-preview-loader').hide();
    });
}


