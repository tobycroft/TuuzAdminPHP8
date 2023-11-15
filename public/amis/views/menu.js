(function () {
  let amisLib = amisRequire('amis');
  let amis = amisRequire('amis/embed');

  // 通过替换下面这个配置来生成不同页面
  let amisJSON = {
    type: 'page',
    title: '模块列表',
    body: {
      type: 'crud',
      name: 'myCRUD',
      syncLocation: false,
      // pageField: "pageIndex",
      // perPageField: "limit",

      // expandConfig: {
      //   expand: "first",
      //   accordion: true
      // },

      // draggable: true,
      saveOrderApi: "admin/menu/saveOrder",
      columnsTogglable:false,
      autoJumpToTopOnPagerChange: true,
      api: "admin/menu/getAll",
      headerToolbar:['bulkActions',
        {
          type: "button",
          actionType: "dialog",
          label: "新增",
          level: "primary",
          dialog: {
            title: "新增菜单",
            body: {
              type: "form",
              api: "admin/menu/insert",

              body:[
                {type:"select",name:"pid",label:"上级",
                  source:'admin/menu/search?limit=100',
                  labelField:'name',
                  valueField:'id',
                  menuTpl: "${id}、${name},${pid}",
                },
                {type:"input-text",name:"name",label:"名称",required: true},
                {type:"input-text",name:"code",label:"代码"},
                {type:"input-text",name:"table",label:"table"},
                {type:"input-text",name:"url",label:"网址"},
                {type:"input-text",name:"path",label:"路径"},
                {type:"input-text",name:"group",label:"分组"},
                {type:"input-text",name:"amis_api",label:"amis_api"},
                {type:"input-text",name:"amis_json",label:"amis_json"},
                {type:"input-text",name:"amis_url",label:"amis_url"},
              ]
            }
          }
        },
        {
          label: "",icon: "fa fa-repeat",type: "button",actionType: "reload",target: "myCRUD",align: "right"
        }
      ],
      // itemActions:[
      //   {
      //     type: "button",
      //     label: "查看",
      //     actionType: "dialog",
      //     dialog: {
      //       title: "查看",
      //       body: '1212'
      //     }
      //   }
      // ],
      footerToolbar: ["switch-per-page","statistics","pagination"],
      quickSaveApi: "admin/menu/amisUpdate",
      quickSaveItemApi: "admin/menu/amisUpdate",

      columns: [
        {name: "id",label: "ID",type: "text",searchable: true},
        {name: "idx",label: "排序",quickEdit: true,searchable: true},
        {name: "pid",label: "上级",searchable: true,quickEdit: true},
        {
          name: "name",
          label: "名称",
          searchable: true,
          saveImmediately: true,
          quickEdit: true
        },
        {
          name: "code",
          searchable: true,
          label: "代码"
        },
        {
          name: "table",
          label: "table",
          saveImmediately: true,
          quickEdit: true
        },
        {
          name: "url",
          searchable: true,
          label: "网址",
          quickEdit: true
        },
        {name: "amis_path",searchable: true,label: "amis_path",quickEdit: true},
        {name: "amis_api",searchable: true,label: "amis_api",quickEdit: true},
        {name: "amis_url",searchable: true,label: "amis_url",quickEdit: true},
        {
          name: "group",
          label: "分组",
          quickEdit: true
        },
        {
          type: "operation",
          label: "操作",
          buttons: [
            {
              actionType: "dialog",
              label: "编辑",
              level: "primary",
              dialog: {
                title: "编辑菜单",
                body: {
                  type: "form",
                  api: "admin/menu/update",
                  body:[
                    {type:"hidden",name:"id"},
                    {type:"input-text",name:"pid",label:"上级PID",required: true},
                    {type:"input-text",name:"name",label:"名称",required: true},
                    {type:"input-text",name:"code",label:"代码"},
                    {type:"input-text",name:"table",label:"table"},
                    {type:"input-text",name:"url",label:"网址"},
                    {type:"input-text",name:"amis_path",label:"amis_path"},
                    {type:"input-text",name:"amis_api",label:"amis_api"},
                    {type:"input-text",name:"amis_url",label:"amis_url"},
                  ]
                }
              }
            },
            {
              label: "删除",
              type: "button",
              actionType: "ajax",
              level: "danger",
              confirmText: "确认要删除？",
              api: "admin/menu/remove"
            },
            {
              label: "复制",
              type: "button",
              actionType: "ajax",
              confirmText: "确认要复制？",
              api: "admin/menu/copy"
            }
          ]
        }
      ],
      // type: 'form',
      // mode: 'horizontal',
      // api: '/saveForm',
      // body: [
      //   {
      //     label: 'Name',
      //     type: 'input-text',
      //     name: 'name'
      //   },
      //   {
      //     label: 'Email',
      //     type: 'input-email',
      //     name: 'email'
      //   }
      // ]
    }
  };
  let amisScoped = amis.embed('#root', amisJSON);
})();