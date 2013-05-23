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

import android.os.Build;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.os.StrictMode;
import android.annotation.SuppressLint;
import android.annotation.TargetApi;
import android.app.Activity;
import android.content.Context;
import android.view.Menu;
import android.view.View;
import android.view.inputmethod.EditorInfo;
import android.view.inputmethod.InputMethodManager;
import android.view.KeyEvent;
import android.widget.Button;
import android.widget.LinearLayout;
import android.widget.ProgressBar;
import android.widget.RelativeLayout;
import android.widget.TextView;
import android.widget.EditText;
import android.util.Log;

@SuppressWarnings("unused")
public class ViewNews extends Activity {

	private static final String ACTIVITY_TAG = "ViewNews";
	private ProgressBar circleProgressBar;
	private TextView tvViewNewsTitle, tvViewNewsDatetime, tvViewNewsContent;
	protected static final int handle_loadNews = 0x10001;
	private String nid = null;
	private View ll_nidEnter;
	private EditText etNid;
	private Button btNid;
	private InputMethodManager inputManager;
	private NewsDataModel newsDataModel;
	private HashMap<String, String> news = null;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_view_news);
		findViews();
		setListeners();
		processIntent();
	}

	protected void onStop() {
		super.onStop();
		newsDataModel = null;
	}

	private void findViews() {
		tvViewNewsTitle = (TextView) findViewById(R.id.tvViewNewsTitle);
		tvViewNewsDatetime = (TextView) findViewById(R.id.tvViewNewsDatetime);
		tvViewNewsContent = (TextView) findViewById(R.id.tvViewNewsContent);
		ll_nidEnter = findViewById(R.id.ll_nidEnter);
		etNid = (EditText) findViewById(R.id.etNid);
		btNid = (Button) findViewById(R.id.btNid);
		inputManager = (InputMethodManager) getSystemService(Context.INPUT_METHOD_SERVICE);
		newsDataModel = new NewsDataModel(this);
		circleProgressBar = (ProgressBar) findViewById(R.id.circleProgressBar);
	}

	private void setListeners() {
		btNid.setOnClickListener(btNidClick);
		etNid.setOnEditorActionListener(etNidOnEnter);
	}

	@TargetApi(Build.VERSION_CODES.GINGERBREAD)
	private void processIntent() {
		try {
			Bundle bundle = this.getIntent().getExtras();
			if (bundle == null)
				return;
			nid = bundle.getString("NID");
			ll_nidEnter.setVisibility(View.GONE);
		} catch (Exception e) {
			int lineNum = Thread.currentThread().getStackTrace()[2]
					.getLineNumber();
			Log.e(ACTIVITY_TAG, lineNum + ": " + e.toString());
		}
		loadNewsGUI();
	}

	private void showNews(String nid) {
		HashMap<String, String> news;
		String sa = null;
		try {
			Log.d("taichunmin", nid);
			// 檢查 NID 的正確性
			if (nid == null || nid.length() == 0 || nid.equals("0"))
				throw new Exception("Nid Can't be zero.");

			news = newsDataModel.get_news(nid);

			// 設定顯示
			tvViewNewsTitle.setText((String) news.get("title"));
			tvViewNewsDatetime.setText((String) news.get("date"));
			Log.d("taichunmin", "debug");
			// 增加換行
			sa = news.get("content");
			tvViewNewsContent.setText("\n　　" + sa.replace("\n", "\n\n　　"));
			// 隱藏軟體鍵盤
			// inputManager.hideSoftInputFromWindow(getWindow().getCurrentFocus().getWindowToken(),
			// InputMethodManager.HIDE_NOT_ALWAYS);
		} catch (Exception e) {
			int lineNum = Thread.currentThread().getStackTrace()[2]
					.getLineNumber();
			Log.e(ACTIVITY_TAG, lineNum + ": " + e.toString());
		}
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.view_news, menu);
		return true;
	}

	private Button.OnClickListener btNidClick = new Button.OnClickListener() {
		@Override
		public void onClick(View v) {
			try {
				showNews(etNid.getText().toString());
			} catch (Exception e) {
				int lineNum = Thread.currentThread().getStackTrace()[2]
						.getLineNumber();
				Log.e(ACTIVITY_TAG, lineNum + ": " + e.toString());
			}
		}
	};
	private TextView.OnEditorActionListener etNidOnEnter = new TextView.OnEditorActionListener() {
		@Override
		public boolean onEditorAction(TextView v, int actionId, KeyEvent event) {
			if (actionId == EditorInfo.IME_ACTION_SEARCH
					|| // Search action
					actionId == EditorInfo.IME_ACTION_DONE
					|| // Done action
					actionId == EditorInfo.IME_NULL
					&& event.getAction() == KeyEvent.ACTION_DOWN) // Enter
			{
				btNid.performClick(); // 模擬 btNid 按下的動作
			}
			return true;
		}

	};

	private void showProgressBar() {
		circleProgressBar.setVisibility(View.VISIBLE);
		circleProgressBar.setProgress(0);
	}

	private void hideProgressBar() {
		circleProgressBar.setVisibility(View.GONE);
	}

	private void loadNewsGUI() {

		showProgressBar();
		if (nid == null || nid.length() == 0 || nid.equals("0"))
			return;

		Thread mThread = new Thread(new Runnable() {

			public void run() {
				try {
					String sa;
					news = newsDataModel.get_news(nid);
					sa = news.get("content");
					sa = "\n　　" + sa.replace("\n", "\n\n　　"); // 增加換行
					news.put("content", sa);

					Message msg = new Message();
					msg.what = handle_loadNews;
					mHandler.sendMessage(msg);
				} catch (Exception e) {
					Log.e(ACTIVITY_TAG, e.getMessage());
				}
			}
		});
		mThread.start();
	}

	private void loadNewsGUIHandle() {
		if(news==null) return;
		// 設定顯示
		tvViewNewsTitle.setText((String) news.get("title"));
		tvViewNewsDatetime.setText((String) news.get("date"));
		tvViewNewsContent.setText((String) news.get("content"));
		hideProgressBar();
	}

	@SuppressLint("HandlerLeak")
	private Handler mHandler = new Handler() {
		public void handleMessage(Message msg) {
			switch (msg.what) {
			case handle_loadNews:
				loadNewsGUIHandle();
				break;
			}
		}
	};
}
