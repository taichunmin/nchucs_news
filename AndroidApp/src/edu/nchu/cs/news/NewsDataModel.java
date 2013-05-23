package edu.nchu.cs.news;

import java.io.BufferedReader;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.util.ArrayList;
import java.util.HashMap;

import org.apache.http.HttpResponse;
import org.apache.http.client.HttpClient;
import org.apache.http.client.methods.HttpGet;
import org.apache.http.impl.client.DefaultHttpClient;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import android.content.Context;
import android.content.Intent;
import android.database.SQLException;
import android.util.Log;
import android.widget.Toast;

public class NewsDataModel {

	private NewsDbConnector db;
	Context callerContext;
	private static final String ACTIVITY_TAG = "NewsDataModel";
	private static final String base_url = "http://news.taichunmin.idv.tw/nchucs_news/ajax.php?";

	public NewsDataModel(Context context) {
		callerContext = context;
		db = new NewsDbConnector(context);
		del_cache_over_time();
	}

	public ArrayList<HashMap<String, String>> newslist_today()
			throws JSONException, Exception {
		String token = system_argu("token",null);
		String url = "get=today&token=" + token;
		return get_list(url);
	}

	public ArrayList<HashMap<String, String>> newslist_day(String date)
			throws JSONException, Exception {
		String url = "get=list&date=" + date;
		return get_list(url);
	}

	public ArrayList<HashMap<String, String>> newslist_cate(int rid)
			throws JSONException, Exception {
		String url = "get=list&rid=" + rid;
		return get_list(url);
	}

	public ArrayList<HashMap<String, String>> cnt_day()
			throws JSONException, Exception {

		String url = "get=cnt&group=date";
		JSONObject jsonObj = fetch_srv_files(url);
		ArrayList<HashMap<String, String>> list = new ArrayList<HashMap<String, String>>();

		if (jsonObj.getString("cntCnt").equals("0"))
			return list;

		JSONArray jsonArray = jsonObj.getJSONArray("cnt");
		int cnt = Integer.parseInt(jsonObj.getString("cntCnt"));

		for (int i = 0; i < cnt; i++) {
			JSONObject obj = jsonArray.getJSONObject(i);

			HashMap<String, String> map = new HashMap<String, String>();
			map.put("date", obj.getString("date"));
			map.put("cnt", obj.getString("cnt"));
			list.add((HashMap<String, String>) map);
		}

		return list;
	}

	public ArrayList<HashMap<String, String>> cnt_cate()
			throws JSONException, Exception {

		String url = "get=cnt&group=rid";
		JSONObject jsonObj = fetch_srv_files(url);
		ArrayList<HashMap<String, String>> list = new ArrayList<HashMap<String, String>>();

		if (jsonObj.getString("cntCnt").equals("0"))
			return list;

		JSONArray jsonArray = jsonObj.getJSONArray("cnt");
		int cnt = Integer.parseInt(jsonObj.getString("cntCnt"));

		for (int i = 0; i < cnt; i++) {
			JSONObject obj = jsonArray.getJSONObject(i);

			HashMap<String, String> map = new HashMap<String, String>();
			map.put("rid", obj.getString("rid"));
			map.put("cnt", obj.getString("cnt"));
			list.add((HashMap<String, String>) map);
		}

		return list;
	}

	public void cache_today_news() throws JSONException, Exception {

		ArrayList<HashMap<String, String>> list = newslist_today();
		int cnt = list.size();
		String nid = "0";

		for (int i = 0; i < cnt; i++) {
			HashMap<String, String> map = list.get(i);
			nid = map.get("nid");
			cache_news(nid);
		}

	}

	public String system_argu(String index, String value) {

		try{
			if (value == null) {
				return db.systemGetByIndex(index).get("value");
			}
	
			else {
				try{
					db.systemPut(index, value);
				}
				catch(SQLException e){
					db.systemSet(index, value);
				}
				return null;
			}
		}
		catch(Exception e)
		{
        	int lineNum = Thread.currentThread().getStackTrace()[2].getLineNumber();
            Log.e(ACTIVITY_TAG + ":system_argu", e.toString());
		}
		return null;
	}

	public HashMap<String, String> get_news(String id) {
		HashMap<String, String> map = null;
		try {
			int nid = Integer.parseInt(id);

			if (nid == 0)
				throw new Exception("Nid Can't be zero.");
			
			try
			{
				map = db.newsGetById(nid);
			}
			catch(Exception e)
			{
				if(e.getMessage().equals("sqlNoData")){
					map = cache_news("" + nid);
					db.newsPut(map);
				}
				else throw e;
			}
			
			if(map == null)
				throw new Exception("nid " + id + " error in get_news()");
			
		} catch (Exception e) {
        	int lineNum = Thread.currentThread().getStackTrace()[2].getLineNumber();
            Log.e(ACTIVITY_TAG, lineNum + ": " + e.toString());
		}
    	int lineNum = Thread.currentThread().getStackTrace()[2].getLineNumber();
        Log.e("taichunmin get_news", lineNum + ": " + map.toString());
		return map;
	}

	private ArrayList<HashMap<String, String>> get_list(String url)
			throws JSONException, Exception {

		JSONObject jsonObj = fetch_srv_files(url);
		ArrayList<HashMap<String, String>> list = new ArrayList<HashMap<String, String>>();

		if (jsonObj.getString("listCnt").equals("0"))
			return list;

		JSONArray jsonArray = jsonObj.getJSONArray("list");
		int cnt = Integer.parseInt(jsonObj.getString("listCnt"));

		for (int i = 0; i < cnt; i++) {
			JSONObject obj = jsonArray.getJSONObject(i);

			HashMap<String, String> map = new HashMap<String, String>();
			map.put("nid", obj.getString("nid"));
			map.put("title", obj.getString("title"));
			map.put("date", obj.getString("news_t"));

			list.add((HashMap<String, String>) map);
		}

		return list;
	}

	private HashMap<String, String> cache_news(String nid) {
		HashMap<String, String> map = new HashMap<String, String>();
		try {
			String url = "get=news&nid=" + nid;
			JSONObject jsonObj = fetch_srv_files(url);

			if (jsonObj.getString("newsCnt").equals("0"))
				throw new Exception("No match news.");

			JSONArray jsonArray = jsonObj.getJSONArray("news");
			JSONObject obj = jsonArray.getJSONObject(0);

			map.put("_id", obj.getString("nid"));
			map.put("title", obj.getString("title"));
			map.put("content", obj.getString("article"));
			map.put("date", obj.getString("news_t"));
			map.put("url", obj.getString("url"));

		} catch (Exception e) {
        	int lineNum = Thread.currentThread().getStackTrace()[2].getLineNumber();
            Log.e(ACTIVITY_TAG + ":cache_news", lineNum + ": " + e.toString());
		}
		return map;
	}

	private void del_cache_over_time() {
		int days = 3;
		try {
			days = Integer.parseInt(system_argu("cache_exist_time",null));
		} catch (NumberFormatException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
		ArrayList<String> id_arr = db.getOverTimeNewsId(days);

		for (int i = 0; i < id_arr.size(); i++) {
			db.newsDelById(id_arr.get(i));
		}

	}

	private JSONObject fetch_srv_files(String api_url) throws Exception{

		JSONObject jsonObject = null;
		try {
			String jsonHtml = getUriContent(base_url + api_url);

			if (jsonHtml.length() == 0)
				throw new Exception("Server didn't send json data.");

			jsonObject = new JSONObject(jsonHtml);

			if (jsonObject.has("error"))
				throw new Exception("Server Error: " + jsonObject.getJSONArray("error").join("; "));

		} catch (Exception e) {
			if(e.getMessage().indexOf("Token invaild.")>=0)
			{
				// Token 失效
				Toast.makeText(callerContext, "請登入以繼續...",	Toast.LENGTH_SHORT).show();
				callerContext.startActivity(new Intent().setClass(callerContext,LoginActivity.class));
			}
			else throw e;
		}
		return jsonObject;
	}

	private static String getUriContent(String uri) throws Exception {
		try {
			HttpClient client = new DefaultHttpClient();
			HttpGet httpGet = new HttpGet(uri);
			HttpResponse response = client.execute(httpGet);
			InputStream ips = response.getEntity().getContent();
			BufferedReader buf = new BufferedReader(new InputStreamReader(ips,
					"UTF-8"));

			StringBuilder sb = new StringBuilder();
			String s;
			while (true) {
				s = buf.readLine();
				if (s == null)
					break;
				sb.append(s);
			}
			buf.close();
			ips.close();
			return sb.toString();
		} finally {
			// any cleanup code...
		}
	}
	public boolean isLogin(boolean checkSvr) throws Exception
	{
		if( system_argu("token",null).equals("") )
			return false;
		if(checkSvr)
		{
			JSONObject jsonObject = fetch_srv_files("token=" + system_argu("token",null));
			if( jsonObject.getJSONArray("error").getString(0).equals("Token invaild."))
				return false;
		}
		return true;
	}
}
