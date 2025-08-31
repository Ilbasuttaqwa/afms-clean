"use strict";(()=>{var e={};e.id=3587,e.ids=[3587],e.modules={3524:e=>{e.exports=require("@prisma/client")},145:e=>{e.exports=require("next/dist/compiled/next-server/pages-api.runtime.prod.js")},6249:(e,t)=>{Object.defineProperty(t,"l",{enumerable:!0,get:function(){return function e(t,s){return s in t?t[s]:"then"in t&&"function"==typeof t.then?t.then(t=>e(t,s)):"function"==typeof t&&"default"===s?t:void 0}}})},5484:(e,t,s)=>{s.r(t),s.d(t,{config:()=>E,default:()=>l,routeModule:()=>f});var n={};s.r(n),s.d(n,{default:()=>d});var r=s(1802),a=s(7153),u=s(6249),o=s(3524);!function(){var e=Error("Cannot find module '../../../lib/auth-middleware'");throw e.code="MODULE_NOT_FOUND",e}();let i=new o.PrismaClient;async function c(e,t){try{if("GET"===e.method){let e=await i.$queryRaw`
        SELECT * FROM cloud_config ORDER BY created_at DESC LIMIT 1
      `;if(0===e.length){let e={server_url:process.env.CLOUD_SERVER_URL||"https://your-cloud-server.com/api",api_key:process.env.CLOUD_API_KEY||"your-api-key",sync_interval:parseInt(process.env.SYNC_INTERVAL||"300"),retry_attempts:parseInt(process.env.RETRY_ATTEMPTS||"3"),timeout:parseInt(process.env.TIMEOUT||"30000"),enable_auto_sync:!0,enable_offline_mode:!0,max_offline_records:1e3};return t.status(200).json({success:!0,data:e})}return t.status(200).json({success:!0,data:e[0]})}if("POST"===e.method){let{server_url:s,api_key:n,sync_interval:r,retry_attempts:a,timeout:u,enable_auto_sync:o,enable_offline_mode:c,max_offline_records:d}=e.body;if(!s||!n)return t.status(400).json({success:!1,message:"Server URL and API key are required"});if(r<60||r>3600)return t.status(400).json({success:!1,message:"Sync interval must be between 60 and 3600 seconds"});if(a<1||a>10)return t.status(400).json({success:!1,message:"Retry attempts must be between 1 and 10"});if(u<5e3||u>12e4)return t.status(400).json({success:!1,message:"Timeout must be between 5000 and 120000 milliseconds"});if(d<100||d>1e4)return t.status(400).json({success:!1,message:"Max offline records must be between 100 and 10000"});await i.$executeRaw`
        CREATE TABLE IF NOT EXISTS cloud_config (
          id INT AUTO_INCREMENT PRIMARY KEY,
          server_url VARCHAR(255) NOT NULL,
          api_key VARCHAR(255) NOT NULL,
          sync_interval INT NOT NULL DEFAULT 300,
          retry_attempts INT NOT NULL DEFAULT 3,
          timeout INT NOT NULL DEFAULT 30000,
          enable_auto_sync BOOLEAN NOT NULL DEFAULT TRUE,
          enable_offline_mode BOOLEAN NOT NULL DEFAULT TRUE,
          max_offline_records INT NOT NULL DEFAULT 1000,
          created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
          updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )
      `,await i.$executeRaw`
        INSERT INTO cloud_config (
          server_url, api_key, sync_interval, retry_attempts, timeout,
          enable_auto_sync, enable_offline_mode, max_offline_records
        ) VALUES (
          ${s}, ${n}, ${r}, ${a}, ${u},
          ${o}, ${c}, ${d}
        )
      `;let l=await i.$queryRaw`
        SELECT * FROM cloud_config ORDER BY created_at DESC LIMIT 1
      `;return t.status(201).json({success:!0,message:"Cloud configuration saved successfully",data:l[0]})}if("PUT"===e.method){let{id:s}=e.query,{server_url:n,api_key:r,sync_interval:a,retry_attempts:u,timeout:o,enable_auto_sync:c,enable_offline_mode:d,max_offline_records:l}=e.body;if(!s)return t.status(400).json({success:!1,message:"Configuration ID is required"});if(!n||!r)return t.status(400).json({success:!1,message:"Server URL and API key are required"});await i.$executeRaw`
        UPDATE cloud_config SET
          server_url = ${n},
          api_key = ${r},
          sync_interval = ${a},
          retry_attempts = ${u},
          timeout = ${o},
          enable_auto_sync = ${c},
          enable_offline_mode = ${d},
          max_offline_records = ${l},
          updated_at = CURRENT_TIMESTAMP
        WHERE id = ${parseInt(s)}
      `;let E=await i.$queryRaw`
        SELECT * FROM cloud_config WHERE id = ${parseInt(s)}
      `;if(0===E.length)return t.status(404).json({success:!1,message:"Configuration not found"});return t.status(200).json({success:!0,message:"Cloud configuration updated successfully",data:E[0]})}if("DELETE"===e.method){let{id:s}=e.query;if(!s)return t.status(400).json({success:!1,message:"Configuration ID is required"});return await i.$executeRaw`
        DELETE FROM cloud_config WHERE id = ${parseInt(s)}
      `,t.status(200).json({success:!0,message:"Cloud configuration deleted successfully"})}return t.status(405).json({success:!1,message:"Method not allowed"})}catch(e){return t.status(500).json({success:!1,message:"Internal server error"})}finally{await i.$disconnect()}}let d=Object(function(){var e=Error("Cannot find module '../../../lib/auth-middleware'");throw e.code="MODULE_NOT_FOUND",e}())(c),l=(0,u.l)(n,"default"),E=(0,u.l)(n,"config"),f=new r.PagesAPIRouteModule({definition:{kind:a.x.PAGES_API,page:"/api/devices/cloud-config",pathname:"/api/devices/cloud-config",bundlePath:"",filename:""},userland:n})},7153:(e,t)=>{var s;Object.defineProperty(t,"x",{enumerable:!0,get:function(){return s}}),function(e){e.PAGES="PAGES",e.PAGES_API="PAGES_API",e.APP_PAGE="APP_PAGE",e.APP_ROUTE="APP_ROUTE"}(s||(s={}))},1802:(e,t,s)=>{e.exports=s(145)}};var t=require("../../../webpack-api-runtime.js");t.C(e);var s=t(t.s=5484);module.exports=s})();