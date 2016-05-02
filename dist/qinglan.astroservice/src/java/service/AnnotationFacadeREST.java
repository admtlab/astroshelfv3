/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package service;

import admt.message.ConfluenceCommunications;
import admt.message.DataMessage;
import com.sun.grizzly.websockets.WebSocket;
import com.sun.grizzly.websockets.WebSocketAdapter;
import com.sun.grizzly.websockets.WebSocketClient;
import entity.Annotation;
import entity.Liveinterest;
import entity.ObjectInfo;
import entity.PrefQT;
import java.io.IOException;
import java.util.List;
import java.util.logging.Level;
import java.util.logging.Logger;
import javax.ejb.Stateless;
import javax.persistence.EntityManager;
import javax.persistence.PersistenceContext;
import javax.persistence.TypedQuery;
import javax.ws.rs.*;
import javax.ws.rs.core.Response;
import org.codehaus.jackson.map.ObjectMapper;
import utilities.Assist;
import utilities.GenerateException;

/**
 *
 * @author roxy
 */
@Stateless
@Path("annotation")
public class AnnotationFacadeREST extends AbstractFacade<Annotation> {
    @PersistenceContext(unitName = "astroservicePU")
    private EntityManager em;
    private final double EPSILON =0.001;

    public AnnotationFacadeREST() {
        super(Annotation.class);
    }

    @POST
    @Path("add")
    @Consumes({"application/xml", "application/json"})
    public Annotation createAndReturn(Annotation entity) {
        super.create(entity);
        getEntityManager().flush();
        //entity = super.find(entity.getAnnoId());
        getEntityManager().refresh(entity);
        
        ConfluenceCommunications cm = new ConfluenceCommunications();
        cm.sendNewAnnotation(entity);
        
        return entity;
    }

    @PUT
    @Override
    @Consumes({"application/xml", "application/json"})
    public void edit(Annotation entity) {
        super.edit(entity);
    }

    @POST
    @Path("{id}/delete")
    public void remove(@PathParam("id") Long id) {
        super.remove(super.find(id));
    }
    
    
    /*
     * @desc: retrieve annotation with anId=id
     * @param: active_user -> id of the user submitting the query
     * @return: an annotation with a particular ID
     */

    @GET
    @Path("{id}")
    @Produces({"application/xml", "application/json"})
    public Response find(@PathParam("id") Long id,
                           @DefaultValue("0")
                           @QueryParam("active_user") Long active_user) throws GenerateException {
        
        /*
         * FIXME: do privacy protection
         * if(active_user==0)
            throw new GenerateException("ACTIVE_USER id is required !");
         *
         */
        Annotation obj = super.find(id);
        if(obj == null)
            return null;
        return Response.status(Response.Status.OK).entity(obj).header("Access-Control-Allow-Origin", "*").build();
    }

    @GET
    @Override
    @Produces({"application/xml", "application/json"})
    public List<Annotation> findAll() {
        return super.findAll();
    }

    @GET
    @Path("{from}/{to}")
    @Produces({"application/xml", "application/json"})
    public List<Annotation> findRange(@PathParam("from") Integer from, @PathParam("to") Integer to) {
        return super.findRange(new int[]{from, to});
    }

    //
    // START PRIMITIVE ASPECTS
    //
    
    /*
     * @desc: search over annotations
     * @param: active_user -> the id of the user who query the annotations
     *         keyword -> a keyword to be found on the annotation value
     *         type -> the type of annotation
     *         first_name -> search annotations from users with this first name
     *         group_name -> search annotations from this group
     *         username -> search annotations from this user
     *         
     *         NOTE: this are not in here because I reached the limit of how many parameters can be :-/
     *         user_id -> id of the user who inserted the annotation
     *         group_id -> id of a group from which users inserted annotations
     *         last_name -> search annotations from users with this last name
     *         
     */
    
    @GET
    @Path("search")
    @Produces({/*"application/xml",*/ "application/json"})
    public /*List<Annotation> */Response search( 
            @DefaultValue("0")
            @QueryParam("active_user") Long active_user,
            @DefaultValue("")
            @QueryParam("keyword") String keyword,
            @DefaultValue("")
            @QueryParam("type") String type,
            @DefaultValue("")
            @QueryParam("target_type") String targetType,
            @DefaultValue("")
            @QueryParam("first_name") String first_name,
            @DefaultValue("")
            @QueryParam("username") String username,
            @DefaultValue("")
            @QueryParam("group_name") String group_name,
            @DefaultValue("")
            @QueryParam("last_name") String last_name,
            @DefaultValue("0")
            @QueryParam("user_id") Long user_id,
            @DefaultValue("100")
            @QueryParam("limt") Integer limit,
            @DefaultValue("false")
            @QueryParam("usePref")Boolean usePreferences,
            @DefaultValue("1000")
            @QueryParam("ra")Double ra,
            @DefaultValue("1000")
            @QueryParam("dec")Double dec,
            @DefaultValue("1000")
            @QueryParam("raFrom")Double raFrom,
            @DefaultValue("1000")
            @QueryParam("raTo")Double raTo,
            @DefaultValue("1000")
            @QueryParam("decFrom")Double decFrom,
            @DefaultValue("1000")
            @QueryParam("decTo") Double decTo
            ) throws GenerateException
    {
        
        
        //FIXME: do privacy protection
        if(usePreferences==true && active_user==0)
            throw new GenerateException("ACTIVE_USER id is required !");
    
        String[][] attr ={{"a.annoValue",keyword},{"a.annoTypeId.annoTypeName",type}, 
                          {"a.userId.fname",first_name}, {"a.userId.username", username},
                          {"a.userId.lname",last_name}, {"a.targetType",targetType} };
        
        
        String q ="SELECT a from Annotation a JOIN a.objectInfoCollection o ";
        Boolean isFirst=true;
        
        for(int i=0; i<attr.length; i++)
        {
            if (!attr[i][1].equals(""))
            {
                if (isFirst)
                {
                    q =q + " WHERE ";
                    isFirst =false;
                }
                else
                    q =q + " AND ";
                
                q =q+attr[i][0]+" LIKE \"%"+attr[i][1]+"%\"";
            }   
        }
        
       if (user_id!=0)
        {
            q =q+Assist.getSqlConnector(isFirst)+" a.userId.userId ="+user_id;
            isFirst =false;
        }
       
       /* SEARCH FOR OBJ WITH RA=ra */
       boolean useJoin =true;
       
       if (ra!=1000)
       {
           q =Assist.createSql(q, " o.ra>= "+(ra-EPSILON)+" AND o.ra <= "+(ra+EPSILON), isFirst);
           isFirst =false;
           useJoin=false;;
       }
       
       /* SEARCH FOR OBJECTS WITH DEC=dec */
       if (dec!=1000 && useJoin)
       {
           q =Assist.createSql(q, " o.dec >= "+(dec-EPSILON)+" AND o.dec <= "+(dec+EPSILON), isFirst);
           isFirst =false;
           useJoin=false;
       }
       else if (dec!=1000)
           q = q+ " AND o.dec >= "+(dec-EPSILON)+" AND o.dec <= "+(dec+EPSILON);
       
       /* SEARCH FOR RANGE --RA: FROM*/
       
       if (raFrom!=1000 && useJoin)
       {
           q =Assist.createSql(q, " o.ra>= "+raFrom, isFirst);
           isFirst =false;
           useJoin=false;
       }
       else if(raFrom!=1000)
                q =q+" AND o.ra >="+raFrom;
       
       /* SEARCH FOR RANGE --RA: TO*/
       if (raFrom!=1000 && useJoin)
       {
           q =Assist.createSql(q, " o.ra<= "+raTo, isFirst);
           isFirst =false;
           useJoin=false;
       }
       else if(raFrom!=1000)
                q =q+" AND o.ra <="+raTo;
       
       /* SEARCH FOR RANGE --DEC: FROM*/
       if (decFrom!=1000 && useJoin)
       {
           q =Assist.createSql(q, " o.dec>= "+decFrom, isFirst);
           isFirst =false;
           useJoin=false;
       }
       else if(decFrom!=1000)
                q =q+" AND o.dec >="+decFrom;
       
       /* SEARCH FOR RANGE --DEC: TO*/
       if (decFrom!=1000 && useJoin)
       {
           q =Assist.createSql(q, " o.dec<= "+decTo, isFirst);
           isFirst =false;
           useJoin=false;
       }
       else if(decFrom!=1000)
                q =q+" AND o.dec <="+decTo;
       
         
        List<PrefQT> prefList=null;
        TypedQuery<Annotation> query;
        List<Annotation> result =null;
        
        if (usePreferences==true)
        {
            prefList =findAllQTByUserId("SELECT qt FROM PrefQT qt WHERE qt.userId.userId="+active_user+" ORDER BY qt.intensity DESC");
            
            String tempQuery =q;
            for (int i=0; i<prefList.size(); i++)
            {
                if (isFirst)
                    tempQuery =q+" WHERE ";
                else
                    tempQuery =q+" AND ";
                
                String predicate =prefList.get(i).getPredicate();
                //FIXME: if prededicate contains o. then JOIN with objCollection
                //       if preficate contains s.  --> do something with surveys
                tempQuery=tempQuery+predicate;
                query =getEntityManager().createQuery(tempQuery, Annotation.class).setMaxResults(1000);
                result =query.getResultList();

                if (result.size() > 0)
                    break;
            }
        }
        else
        {
             query =getEntityManager().createQuery(q, Annotation.class);
             Logger.getLogger(AnnotationFacadeREST.class.getName()).log(Level.INFO, "query: " + query.toString());
             query.setMaxResults(limit);
             result =query.getResultList();
        }
      
       return Response.status(Response.Status.OK).entity(result).header("Access-Control-Allow-Origin", "*").build();
       //return result;
        
    }
    
    /*
     * @desc: return all annotations inserted in the past *time_window* days
     * @param: active_user: required for privacy control
     *         time_window: measured in days
     * @return: a list of annotations
     */
    @GET
    @Path("recent")
    @Produces({"application/xml", "application/json"})
    public List<Annotation> find(@DefaultValue("0")
                           @QueryParam("active_user") Long active_user,
                           @DefaultValue("30")
                           @QueryParam("time_window") double time_window) throws GenerateException 
    {
     
        /*
         * FIXME: do privacy protection
         * if(active_user==0)
            throw new GenerateException("ACTIVE_USER id is required !");
         *
         */
        
        TypedQuery<Annotation> query =getEntityManager().createNamedQuery("nativeSQL.findRecent",Annotation.class).setParameter(1, time_window);
        return query.getResultList();
     }

    @java.lang.Override
    protected EntityManager getEntityManager() {
        return em;
    }
    
    
    @GET
    @Path("count")
    @Produces("text/plain")
    public String countREST() {
        return String.valueOf(super.count());
    }
    
    
    //
    // START ASPECTS
    //
    
    /*
     * @desc: returns all objects that are linked to one particular annotation
     * @param: active_user -> the userID that submitted the query
     *         object_id -> id of the object as given in the database or as it appears in a survey; 
     * @returns: a list of objects
     *         
     */
    
    @GET
    @Path("{id}/objects")
    @Produces({"application/xml", "application/json"})
    public List<ObjectInfo> getAnnotationsByObject(
            @PathParam("id") Long annId,
            @DefaultValue("0")
            @QueryParam("active_user") Long active_user,
            @DefaultValue("")
            @QueryParam("object_id") String object_id
            ) throws GenerateException {
        
        /*
         * FIXME: do privacy protection
         * if(active_user==0)
            throw new GenerateException("ACTIVE_USER id is required !");
         *
         */
        
        String q ="SELECT o FROM Annotation a JOIN a.objectInfoCollection o WHERE a.annoId ="+annId;
        long objId=0;
        
        if (!object_id.equals(""))
        {
            try{
               objId=Long.valueOf(object_id);
               q =q+" AND o.objectId="+objId;
            }catch(NumberFormatException e)
            {
                q =q+" AND o.surveyObjId LIKE \"%"+object_id +"%\"";
            }
        }
        
        TypedQuery<ObjectInfo> query =getEntityManager().createQuery(q, ObjectInfo.class);
        return query.getResultList();
    }
    public List<PrefQT> findAllQTByUserId(String q) {
        
        javax.persistence.TypedQuery<PrefQT> query =getEntityManager().createQuery(q, PrefQT.class);
        List<PrefQT> result=query.getResultList();
        
        return result;
    }
    
}
