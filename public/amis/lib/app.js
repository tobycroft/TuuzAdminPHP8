
(function () {
  if (window.easy) {
    easy.defaults.baseURL = ENV.baseUrl;
    easy.defaults.tokenField = 'token';
    easy.defaults.successStatusField = 0;
    easy.defaults.header['token'] = easy.store("?"+ENV.tokenField)?easy.store(ENV.tokenField):'';
  }

  // console.log(easy.store('token'));
 

  if (window.amis) {
    // console.log(VeryAxios)
    let amisLib = amisRequire('amis');
    let amis = amisRequire('amis/embed');

    let def = {
      method: 'POST', // 必须大写
      success(r,res){
        // console.log(r);
      },
      error(r){
        amisLib.toast.info(r.message);
      },
      error404(r){},
      error401(r){
        amisLib.toast.info(r.message);
        setTimeout(() => {
          location.href = ENV.loginUrl
        }, 800);
      },
      data:{
      }
    };



    amis.embed(
      '#root',
      {
        // amis schema
      },
      {
        // 这里是初始 props，一般不用传。
        // locale: 'en-US' // props 中可以设置语言，默认是中文
      },
      {
        // 可以不传，用来实现 ajax 请求
        fetcher2: (params) => {
          let sg = easyHttp.extends(def,params);
          return easyHttp.post(sg).then(resp => { 
            let {r,res} = resp;
            if(res.status == 400){
              let data = {
                status: -1,
                msg: res.message,
                data: {
                  items: [],
                }
              }
              return data;
            }
            let status = res[ENV.dataStatusField];
            let code = 0;

            let data = {
              status: code,
              msg: res.message,
              data: {
                items: [],
                total:0
              }
            }
            if (XEUtils.has(res, 'data.value')){
              data['data']['value'] = res.data.value;
            }
            if (XEUtils.has(res, 'data.list')) {
              data['data']['items'] = res.data.list;
              data['data']['total'] = res.data.total;
            }else {
              if (res.data){
                data['data'] = res.data
              }else{
                data['data'] = {
                  status: code,
                  msg: res.message,
                }
              }

            }
            return data;
          })


        },

        // adaptor(payload, response){
        //   console.log(payload);
        //   return {
        //     ...payload,
        //     status: payload.code === 200 ? 0 : payload.code
        //   };
        // },
        requestAdaptor(api){
          api.url = ENV.baseUrl+api.url;
          api.headers['token'] = store.get("?"+ENV.tokenField)?store.get(ENV.tokenField):'';
          return api;
        },
        

        // responseAdaptor(api){
        //   console.log(api);
        //   api.url = ENV.baseUrl+api.url;
        //   console.log(api.url);
        //   return {
        //     ...api,
        //     data: {
        //       ...api.data, // 获取暴露的 api 中的 data 变量
        //       foo: 'bar' // 新添加数据
        //     }
        //   };
        // },

        responseAdaptor(api, response, query, request) {
          // if (XEUtils.has(response, 'data.list')) {
          //   response.data = response.data.list;
          // }
          return response;
        },

        // 可以不传，用来接管页面跳转，比如用 location.href 或 window.open，或者自己实现 amis 配置更新
        // jumpTo: to => {
        //   // location.href = to;
        // },

        // 可以不传，用来实现地址栏更新
        // watchRouteChange: fn => {
        //   return history.listen(fn);
        // },
        // updateLocation: (to, replace) => {},


        // 可以不传，用来判断是否目标地址当前地址。
        // isCurrentUrl: url => {},

        // 可以不传，用来实现复制到剪切板
        // copy: content => {},

        // 可以不传，用来实现通知
        // notify: (type, msg) => {},

        // 可以不传，用来实现提示
        // alert: content => {},

        // 可以不传，用来实现确认框。
        // confirm: content => {},

        // theme: 'cxd' 
      }
    );
  }



})();