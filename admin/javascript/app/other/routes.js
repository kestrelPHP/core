/**
 * Created by Nam Dinh on 3/12/2016.
 */
var routes = [];
routes.push({name: 'login', url: '', template: app.conf.templatePath + 'edit.tpl.html'});
routes.push({name: 'edit', url:'edit/:table/:id', template: app.conf.templatePath + 'edit.tpl.html'});
routes.push({name: 'list', url:'list/:table', template: app.conf.templatePath + 'list.tpl.html'});
