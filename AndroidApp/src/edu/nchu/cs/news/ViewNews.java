package edu.nchu.cs.news;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;

import org.apache.http.HttpEntity;
import org.apache.http.HttpResponse;
import org.apache.http.StatusLine;
import org.apache.http.client.ClientProtocolException;
import org.apache.http.client.HttpClient;
import org.apache.http.client.methods.HttpGet;
import org.apache.http.impl.client.DefaultHttpClient;
import org.json.JSONArray;
import org.json.JSONObject;

import android.os.Bundle;
import android.app.Activity;
import android.content.Context;
import android.view.Menu;
import android.view.View;
import android.view.inputmethod.EditorInfo;
import android.view.inputmethod.InputMethodManager;
import android.view.KeyEvent;
import android.widget.Button;
import android.widget.TextView;
import android.widget.EditText;
import android.util.Log;

public class ViewNews extends Activity {

	private TextView tvViewNewsTitle;
	private TextView tvViewNewsDatetime;
	private TextView tvViewNewsContent;
	private EditText etNid;
	private Button btNid;
	private InputMethodManager inputManager;
	
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_view_news);
		
		tvViewNewsTitle = (TextView) findViewById( R.id.tvViewNewsTitle );
		tvViewNewsDatetime = (TextView) findViewById( R.id.tvViewNewsDatetime );
		tvViewNewsContent = (TextView) findViewById( R.id.tvViewNewsContent );
		etNid = (EditText) findViewById( R.id.etNid );
		btNid = (Button) findViewById( R.id.btNid );
		inputManager = (InputMethodManager) getSystemService(Context.INPUT_METHOD_SERVICE);
		
		btNid.setOnClickListener(btNidClick);
		etNid.setOnEditorActionListener(etNidOnEnter);
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.view_news, menu);
		return true;
	}
	
	public static String getUriContent(String uri) throws Exception {
		try {
			HttpClient client = new DefaultHttpClient();
			HttpGet httpGet = new HttpGet(uri);
			HttpResponse response = client.execute(httpGet);
			InputStream ips  = response.getEntity().getContent();
			BufferedReader buf = new BufferedReader(new InputStreamReader(ips,"UTF-8"));

			StringBuilder sb = new StringBuilder();
			String s;
			while(true)
			{
				s = buf.readLine();
				if(s==null) break;
				sb.append(s);
			}
			buf.close();
			ips.close();
			return sb.toString();
		} 
		finally {
			// any cleanup code...
		}
	}
	
	private Button.OnClickListener btNidClick = new Button.OnClickListener()
	{
		@Override
		public void onClick(View v) {
			try
			{
				int nid = Integer.parseInt(etNid.getText().toString());
				// 檢查 NID 的正確性
				if(nid == 0) throw new Exception("Nid Can't be zero.");
				String jsonHtml = getUriContent( "http://news.taichunmin.idv.tw/nchucs_news/ajax.php?get=news&nid=" + nid );
				// 檢查從伺服器取得的 json 不為空
				if(jsonHtml.length()==0) throw new Exception("Server didn't send json data.");
				JSONObject jsonObject = new JSONObject(jsonHtml);
				// 確認 json 中不含錯誤訊息
				if(!jsonObject.isNull("error"))
					throw new Exception("Server Error.");
				// 檢查 news 的數量
				if(jsonObject.getString("newsCnt").equals("0"))
					throw new Exception("No match news.");
				
				// 取得新聞的 json Object
				JSONArray jsonArray = jsonObject.getJSONArray("news");
				// 只取一個，正常來說需要用迴圈
				JSONObject news = jsonArray.getJSONObject(0);
				
				// 設定顯示
				tvViewNewsTitle.setText(news.getString("title"));
				tvViewNewsDatetime.setText(news.getString("news_t"));
				// 增加換行
				tvViewNewsContent.setText("\n　　"+news.getString("article").replace("\n", "\n\n　　"));
				
				// 隱藏軟體鍵盤
				inputManager.hideSoftInputFromWindow(getWindow().getCurrentFocus().getWindowToken(), InputMethodManager.HIDE_NOT_ALWAYS);
			}
			catch( Exception e )
			{
				tvViewNewsContent.setText("錯誤：有什麼東西錯誤了。\n\nNID: " + etNid.getText().toString()+ "\n\n" + e.toString().replaceFirst("java.lang.Exception: ", ""));
				// 顯示錯誤訊息，而且不要顯示 java.lang.Exception: 的文字(過於常見)
			}
		}
	};
	private TextView.OnEditorActionListener etNidOnEnter = new TextView.OnEditorActionListener()
	{
		@Override
		public boolean onEditorAction(TextView v, int actionId, KeyEvent event) {
			if (actionId == EditorInfo.IME_ACTION_SEARCH ||	// Search action
		        actionId == EditorInfo.IME_ACTION_DONE ||	// Done action
		        actionId == EditorInfo.IME_NULL && event.getAction() == KeyEvent.ACTION_DOWN)	// Enter
			{ 
				btNid.performClick();	// 模擬 btNid 按下的動作
			}
			return true;
		}
		
	};
}
