/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package utilities;

/**
 *
 * @author roxy
 */
public class Assist {
    
    public static String getSqlConnector(boolean isFirst)
    {
        if (isFirst)
            return " WHERE ";
        else
            return " AND ";
    }
    
    public static <T> String createSql(String q, String addCond, boolean isFirst)
    {
           String postfix="";
           String prefix;
           
           int indxFrom =q.indexOf("WHERE");
           if (indxFrom!=-1)
           {
                prefix =q.substring(0, indxFrom);
                postfix =q.substring(indxFrom);
                q =prefix;
           }
           
           //q =q +" JOIN a.objectInfoCollection o " + postfix + Assist.getSqlConnector(isFirst)+addCond ;
           q =q + postfix + Assist.getSqlConnector(isFirst)+addCond ;
           
           return q;
       
    }
    
}
