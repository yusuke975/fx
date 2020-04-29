import java.io.BufferedReader;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.net.HttpURLConnection;
import java.net.URL;
import java.util.ArrayList;
import java.util.Calendar;
import com.google.gson.*;
import java.sql.*;
import java.text.SimpleDateFormat;
import java.util.Date;

public class Rate {

    public static void main(String[] args) throws Exception {
  
        ArrayList<String> urlList = new ArrayList<String>();
        urlList.add("from_currency=USD&to_currency=JPY");  //ドル円
        urlList.add("from_currency=EUR&to_currency=JPY");
        urlList.add("from_currency=EUR&to_currency=USD");
        urlList.add("from_currency=GBP&to_currency=JPY");
        urlList.add("from_currency=CNY&to_currency=JPY");
        for(int i = 0; i < 5; i++){
            String stringjson = requestHTTP(urlList.get(i));
            System.out.println(stringjson);
            ArrayList<String> arraylist = extract(stringjson); // 
            arraylist.add(0, String.valueOf(i));      //
            updateDataBase(arraylist);
            System.out.println("\n--------------------------------------\n\n\n");
            
        }

    }
    public static String requestHTTP(String stringurl) throws Exception{
        URL url = new URL("https://www.alphavantage.co/query?function=CURRENCY_EXCHANGE_RATE&" + stringurl + "&apikey=**password**");
        HttpURLConnection conn = (HttpURLConnection)url.openConnection();
        conn.setDoOutput(true);
        conn.setRequestMethod("GET");
        conn.connect();
        InputStream in = conn.getInputStream();
  
        BufferedReader br = new BufferedReader( new InputStreamReader(in));
    
        StringBuilder sb = new StringBuilder();
        
        String line;
        
        while ((line = br.readLine()) != null) {
            sb.append(line);
        }
        br.close();
        in.close();

        return sb.toString();
    }
    public static ArrayList<String> extract(String json) throws Exception{
        //json形式から為替レート,Bid,Ask,現在時刻を取得して配列に格納
        Gson gson = new Gson();
        JsonObject root = new Gson().fromJson(json.toString(), JsonObject.class);
        JsonObject array = root.get("Realtime Currency Exchange Rate").getAsJsonObject();
        String rate = array.get("5. Exchange Rate").getAsString();
        String bid = array.get("8. Bid Price").getAsString();
        String ask = array.get("9. Ask Price").getAsString();
        String utcStr = array.get("6. Last Refreshed").getAsString();
        SimpleDateFormat sdFormat = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
        Date utcDate = sdFormat.parse(utcStr);
        Calendar calendar = Calendar.getInstance();
        calendar.setTime(utcDate);
        rate = rate.substring(0, 7);
        bid = bid.substring(0, 7);
        ask = ask.substring(0, 7);
        Double.parseDouble(rate);
        String nowStr = sdFormat.format(calendar.getTime());
        ArrayList<String> list = new ArrayList<String>();
        list.add(rate);
        list.add(bid);
        list.add(ask);
        list.add(nowStr);
        return list;
    }
    public static void updateDataBase(ArrayList<String> array){
        Connection conn = null;
        System.out.println(array);
        try {
        conn = DriverManager.getConnection("jdbc:mysql://localhost/fx", "root", "**password**");
        Statement stmt = conn.createStatement();
        String sql = "INSERT INTO rate VALUES (" + array.get(0) + ", \"" + array.get(1) + "\", \"" + array.get(2) + "\", \"" + array.get(3) +"\", \"" + array.get(4) + "\" )" ;
        stmt.executeUpdate(sql);
        }catch (SQLException e){
            System.out.println("SQLException:" + e.getMessage());
        }

    }

}
