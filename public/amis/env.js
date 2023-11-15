var ENV = {
  name:'后台管理系统',
  title:'后台管理系统',
  logo:'',
  welcome:'欢迎语言',
  version:'1.0.0',
  copyright:"版权信息",
  beian:'豫ICP备20008040号-2',

  debug:false,
  env: 'debug',
  baseUrl : 'https://amisadmin.tieninc.com/index.php/',
  loginUrl:'login.html',

  tokenField:'token',

  headerFields:{
    token:'',
  },
  dataStatusField:'status', //返回数据状态码字段名称
  successStatusField:200, //成功状态值

  // POST传递扩展字段 _os：系统信息，_envCode：预览版 正式版 ，_version：版本号，_page：当前path，_apid：小程序ID
  // 可以自定义  默认从 Storage 里面取
  extendsPostFields:['_os','_platform','_envCode','_version','_page','_apid','_amis'],
  extendsGetFields:[],
  extendsHeaderFields:[],

}
