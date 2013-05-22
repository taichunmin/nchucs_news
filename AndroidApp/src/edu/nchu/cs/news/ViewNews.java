package edu.nchu.cs.news;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.util.HashMap;

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
import android.widget.LinearLayout;
import android.widget.RelativeLayout;
import android.widget.TextView;
import android.widget.EditText;
import android.util.Log;

public class ViewNews extends Activity {

	private static final String ACTIVITY_TAG="ViewNews";
	private TextView tvViewNewsTitle,tvViewNewsDatetime,tvViewNewsContent;
	private View ll_nidEnter;
	private EditText etNid;
	private Button btNid;
	private InputMethodManager inputManager;
	private NewsDataModel newsDataModel;
	
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_view_news);
		findViews();
		setListeners();
		showNewsFromIntent();
	}
	
	private void findViews()
	{
		tvViewNewsTitle = (TextView) findViewById( R.id.tvViewNewsTitle );
		tvViewNewsDatetime = (TextView) findViewById( R.id.tvViewNewsDatetime );
		tvViewNewsContent = (TextView) findViewById( R.id.tvViewNewsContent );
		ll_nidEnter = findViewById( R.id.ll_nidEnter );
		etNid = (EditText) findViewById( R.id.etNid );
		btNid = (Button) findViewById( R.id.btNid );
		inputManager = (InputMethodManager) getSystemService(Context.INPUT_METHOD_SERVICE);
		newsDataModel = new NewsDataModel(this);
	}
	
	private void setListeners()
	{
		btNid.setOnClickListener(btNidClick);
		etNid.setOnEditorActionListener(etNidOnEnter);
	}
	
	private void showNewsFromIntent()
	{
		try{
			Bundle bundle = this.getIntent().getExtras();
			showNews(bundle.getString("NID"));
			ll_nidEnter.setVisibility(View.GONE); 
		}
		catch(Exception e)
		{
			Log.e(ACTIVITY_TAG,e.toString());
		}
	}
	
	private void showNews(String nid)
	{
		try{
			// 檢查 NID 的正確性
			if(nid.length()==0 || nid.equals("0")) throw new Exception("Nid Can't be zero.");
			
			HashMap<String,String> news = newsDataModel.get_news(nid);
			
			// 設定顯示
			tvViewNewsTitle.setText(news.get("title"));
			tvViewNewsDatetime.setText(news.get("news_t"));
			// 增加換行
			tvViewNewsContent.setText("\n　　"+news.get("article").replace("\n", "\n\n　　"));
			
			// 隱藏軟體鍵盤
			inputManager.hideSoftInputFromWindow(getWindow().getCurrentFocus().getWindowToken(), InputMethodManager.HIDE_NOT_ALWAYS);
		}
		catch( Exception e )
		{
			Log.e(ACTIVITY_TAG,e.toString());
		}
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.view_news, menu);
		return true;
	}
	
	private Button.OnClickListener btNidClick = new Button.OnClickListener()
	{
		@Override
		public void onClick(View v) {
			try
			{
				showNews(etNid.getText().toString());
			}
			catch( Exception e )
			{
				Log.e(ACTIVITY_TAG,e.toString());
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
