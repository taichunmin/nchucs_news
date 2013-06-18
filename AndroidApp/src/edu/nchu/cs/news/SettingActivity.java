package edu.nchu.cs.news;

import android.os.Bundle;
import android.app.Activity;
import android.view.Menu;
import android.widget.EditText;
import android.widget.Toast;

public class SettingActivity extends Activity {

	EditText et_simi_1st, et_simi_2st, et_simi_3st, et_onto_limit;
	EditText EditTextArray[] = null;
	String setting_default[] = {"60","30","10","100"};
	NewsDataModel newsDataModel = null;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_setting);
		findViews();
		setListeners();
	}
	
	@Override
	protected void onPause() {
		super.onPause();
		boolean errorDiaplay = false;
		for(EditText et: EditTextArray)
		{
			try{
				int val = Integer.parseInt(et.getText().toString());
				if(val<0 || val>100)
					throw new Exception("");
			}catch(Exception e)
			{
				et.setText(setting_default[ indexOf(EditTextArray, et) ]);
				if(!errorDiaplay)
				{
					errorDiaplay = true;
					Toast.makeText(getApplicationContext(), "因為有些資料設定有誤，所以被設定為預設值。",
					Toast.LENGTH_SHORT).show();
				}
			}
		}

		newsDataModel.system_argu("simi_1st", et_simi_1st.getText().toString());
		newsDataModel.system_argu("simi_2st", et_simi_2st.getText().toString());
		newsDataModel.system_argu("simi_3st", et_simi_3st.getText().toString());
		newsDataModel.system_argu("onto_limit", et_onto_limit.getText().toString());
	}

	private void findViews() {
		newsDataModel = new NewsDataModel(this);
		et_simi_1st = (EditText) findViewById(R.id.et_simi_1st);
		et_simi_2st = (EditText) findViewById(R.id.et_simi_2st);
		et_simi_3st = (EditText) findViewById(R.id.et_simi_3st);
		et_onto_limit = (EditText) findViewById(R.id.et_onto_limit);
		
		EditTextArray = new EditText[]{et_simi_1st, et_simi_2st, et_simi_3st, et_onto_limit};

		et_simi_1st.setText( newsDataModel.system_argu("simi_1st", null) );
		et_simi_2st.setText( newsDataModel.system_argu("simi_2st", null) );
		et_simi_3st.setText( newsDataModel.system_argu("simi_3st", null) );
		et_onto_limit.setText( newsDataModel.system_argu("onto_limit", null) );
	}

	private void setListeners() {

	}
	
	public static <T> int indexOf(T theArray[], T o)
	{
		return java.util.Arrays.asList(theArray).indexOf(o);
	}

}
