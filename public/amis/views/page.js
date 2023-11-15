(function () {
  let amisLib = amisRequire('amis');
  let amis = amisRequire('amis/embed');
  let locat = XEUtils.locat();
  let query = locat.search;

  // 通过替换下面这个配置来生成不同页面
  let amisJSON = {
    type: 'service',
    schemaApi: {
      url:"admin/menu/amisPage"+query,
    }
    // body: {
    //   type: 'service',
    //   schemaApi: "admin/AmisPage/page?amis_page_id=4",
    // }
  };
  let amisScoped = amis.embed('#root', amisJSON);
})();