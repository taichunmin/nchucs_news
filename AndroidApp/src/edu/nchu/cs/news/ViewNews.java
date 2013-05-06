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
				// �ˬd NID �����T��
				if(nid == 0) throw new Exception("Nid Can't be zero.");
				String jsonHtml = getUriContent( "http://news.taichunmin.idv.tw/nchucs_news/ajax.php?get=news&nid=" + nid );
				// �ˬd�q���A�����o�� json ������
				if(jsonHtml.length()==0) throw new Exception("Server didn't send json data.");
				JSONObject jsonObject = new JSONObject(jsonHtml);
				// �T�{ json �����t���~�T��
				if(!jsonObject.isNull("error"))
					throw new Exception("Server Error.");
				// �ˬd news ���ƶq
				if(jsonObject.getString("newsCnt").equals("0"))
					throw new Exception("No match news.");
				
				// ���o�s�D�� json Object
				JSONArray jsonArray = jsonObject.getJSONArray("news");
				// �u���@�ӡA���`�ӻ��ݭn�ΰj��
				JSONObject news = jsonArray.getJSONObject(0);
				
				// �]�w���
				tvViewNewsTitle.setText(news.getString("title"));
				tvViewNewsDatetime.setText(news.getString("news_t"));
				// �W�[����
				tvViewNewsContent.setText("\n�@�@"+news.getString("article").replace("\n", "\n\n�@�@"));
				
				// ���ón����L
				inputManager.hideSoftInputFromWindow(getWindow().getCurrentFocus().getWindowToken(), InputMethodManager.HIDE_NOT_ALWAYS);
			}
			catch( Exception e )
			{
				tvViewNewsContent.setText("���~�G������F����~�F�C\n\nNID: " + etNid.getText().toString()+ "\n\n" + e.toString().replaceFirst("java.lang.Exception: ", ""));
				// ��ܿ��~�T���A�ӥB���n��� java.lang.Exception: ����r(�L��`��)
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
				btNid.performClick();	// ���� btNid ���U���ʧ@
			}
			return true;
		}
		
	};
}
