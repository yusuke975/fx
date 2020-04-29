import java.io.BufferedReader;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.net.HttpURLConnection;
import java.net.URL;
import java.util.ArrayList;
import com.google.gson.*;
import java.sql.*;

public class News {

    public static void main(String[] args) throws Exception {

        ArrayList<String> country = new ArrayList<String>();
        country.add("us");
        country.add("jp");
        country.add("cn");
        for(int i = 0; i < 3; i++){
            String stringjson = requestHTTP("http://newsapi.org/v2/top-headlines?country=" + country.get(i) + "&apiKey=**password**");
            ArrayList<Articles> arraylist = extract(stringjson, country.get(i)); 
            insertDataBase(arraylist, country.get(i));
            deleteDataBase(country.get(i));
        }
    }
    public static String requestHTTP(String stringurl) throws Exception{
        URL url = new URL(stringurl);
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
    public static ArrayList<Articles> extract(String json, String country){
        Gson gson = new Gson();
        JsonObject root = new Gson().fromJson(json.toString(), JsonObject.class);

        ArrayList<Articles> list = new ArrayList<Articles>();
        for(int i = 0; i < root.getAsJsonArray("articles").size(); i++ ){
            Articles articles = new Articles();
            JsonElement articleEle = root.getAsJsonArray("articles").get(i);
            JsonObject articleObj = articleEle.getAsJsonObject();
            String  sourceStr = articleObj.get("source").getAsJsonObject().get("name").toString();
            articles.setSource(sourceStr);
            String timeStr = articleObj.get("publishedAt").toString();
            timeStr = timeStr.replace("T", " ").replace("Z", "");   // この形「2020-03-29T06:03:43Z」 を 「2020-03-29 06:03:43」に変える
            articles.setDateStr(timeStr);

            
            String urlStr = articleObj.get("url").toString();
            articles.seturlStr(urlStr);
            String urlToImageStr = articleObj.get("urlToImage").toString();
            articles.seturlToImageStr(urlToImageStr);
            String descriptionStr = articleObj.get("description").toString();
            
           
                
            if(country == "us"){
                
                if (descriptionStr.indexOf("...") != -1) {
                    descriptionStr = descriptionStr.replace("...", "");
                } 
                
                int sentenceEnd = descriptionStr.lastIndexOf(".");
                if(sentenceEnd > 0){
                    descriptionStr = descriptionStr.substring(1, sentenceEnd + 1);
                    if(descriptionStr.length() < 60){
                        descriptionStr = "";
                    }else{
                        articles.setDescription(descriptionStr);
                        list.add(articles);
                    }           
                }
            }else{
                if(descriptionStr.length() < 60){
                    descriptionStr = "";
                }
                int sentenceEnd = descriptionStr.lastIndexOf("。");
                if(sentenceEnd > 0){
                    descriptionStr = descriptionStr.substring(1, sentenceEnd + 1);   
                    articles.setDescription(descriptionStr); 
                    list.add(articles);
                }               
            }     
        }
        return list;
    }
    public static void insertDataBase(ArrayList<Articles> array, String country){
        
        Connection conn = null;
        

        try {
        conn = DriverManager.getConnection("jdbc:mysql://localhost/fx", "root", "Ycyc2112#");

        Statement stmt = conn.createStatement();
        for(int i = 0; i < array.size(); i++){
            Articles articles = array.get(i);
            System.out.println(articles);
            String sql = "INSERT INTO news" + country + " (source, date, url, urlToImage, description) VALUES(" + articles.getSource() + "," + articles.getDateStr() + "," +  articles.geturlStr()+ "," + articles.geturlToImageStr() + ",\"" + articles.getDescription() + "\")";
            stmt.executeUpdate(sql);
        }
        
        }catch (SQLException e){
            System.out.println("SQLException:" + e.getMessage());
        }
    }
    public static void deleteDataBase(String country){
        //新しい10記事だけ残して、あとはデータベースから削除
        Connection conn = null;
        try {
        conn = DriverManager.getConnection("jdbc:mysql://localhost/fx", "root", "Ycyc2112#");

        Statement stmt = conn.createStatement();
            String sql = "delete from news" + country +" where id NOT IN (select id from (select id from news" + country + "  order by date DESC limit 10) as tmp);";
            stmt.executeUpdate(sql);
        
        }catch (SQLException e){
            System.out.println("SQLException:" + e.getMessage());
        }
    }
       
}
class Articles{
    private String source;   //例 nikkei, bloomberg 
    private String dateStr;
    private String urlStr;
    private String urlToImageStr;
    private String description;
    public String getSource(){
        return source;
    }
   
    public String getDateStr(){
        return dateStr;
    }
    public String geturlStr(){
        return urlStr;
    }
    public String geturlToImageStr(){
        return urlToImageStr;
    }
    public String getDescription(){
        return description;
    }

    public void setSource(String source){
        this.source = source;
    }
    public void setDateStr(String dateStr){
        this.dateStr = dateStr;
    }
    public void seturlStr(String urlStr){
        this.urlStr = urlStr;
    }
    public void seturlToImageStr(String urlToImageStr){
        this.urlToImageStr = urlToImageStr;
    }
    public void setDescription(String description){
        this.description = description;
    }
    public String toString(){
        return "Articlesインスタンスは" + this.source + "|" + this.dateStr + "|" + this.urlStr + "|" + this.urlToImageStr + "|" + this.description +"\n";
    }
}























