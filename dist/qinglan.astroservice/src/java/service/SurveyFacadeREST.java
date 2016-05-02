/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package service;

import entity.ObjectInfo;
import entity.Survey;
import java.util.List;
import javax.ejb.Stateless;
import javax.persistence.EntityManager;
import javax.persistence.PersistenceContext;
import javax.persistence.TypedQuery;
import javax.ws.rs.*;

/**
 *
 * @author roxy
 */
@Stateless
@Path("survey")
public class SurveyFacadeREST extends AbstractFacade<Survey> {
    @PersistenceContext(unitName = "astroservicePU")
    private EntityManager em;

    public SurveyFacadeREST() {
        super(Survey.class);
    }

    @POST
    @Override
    @Consumes({"application/xml", "application/json"})
    public void create(Survey entity) {
        super.create(entity);
    }

    @PUT
    @Override
    @Consumes({"application/xml", "application/json"})
    public void edit(Survey entity) {
        super.edit(entity);
    }

    @DELETE
    @Path("{id}")
    public void remove(@PathParam("id") Long id) {
        super.remove(super.find(id));
    }

    @GET
    @Path("{id}")
    @Produces({"application/xml", "application/json"})
    public Survey find(@PathParam("id") Long id) {
        return super.find(id);
    }

    @GET
    @Override
    @Produces({"application/xml", "application/json"})
    public List<Survey> findAll() {
        return super.findAll();
    }

    @GET
    @Path("{from}/{to}")
    @Produces({"application/xml", "application/json"})
    public List<Survey> findRange(@PathParam("from") Integer from, @PathParam("to") Integer to) {
        return super.findRange(new int[]{from, to});
    }

    
    //
    // START PRIMITIVE ACTIONS
    //
    
    /*
     * @desc: returns all surveys that match one or more particular criterias given as parameters
     * @param: active_user -> required for privacy protection
     *         survey_id -> id of the survey you are searching for.
     *         survey_name -> the name of the survey you are searching for.
     * @reurns: a list of surveys
     */
    @GET
    @Path("search")
    @Produces({"application/xml", "application/json"})
    public List<Survey> findSurveysBySurveyId(
            @QueryParam("active_user") Long active_user,
            @DefaultValue("0")
            @QueryParam("id") Long survey_id,
            @DefaultValue("")
            @QueryParam("name") String survey_name)
    {
        String q="SELECT s from Survey s";
        Boolean isFirst=true;
        
        /*
         * FIXME: use active user for privacy protection
         */
        
        if(survey_id!=0)
        {
            q =q+" WHERE s.surveyId="+survey_id;
            isFirst=false;
        }
        
        if(!survey_name.equals(""))
        {
            if(isFirst)
                q=q+" WHERE ";
            else
                q=q+" AND ";
            
            q=q+" s.surveyName LIKE \"%"+survey_name+"%\"";
        }
       
        
        TypedQuery<Survey> query =getEntityManager().createQuery(q, Survey.class);
        
        return query.getResultList();
    
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
     * @desc: returns all objects from one particular survey
     * @param: active_user -> required for privacy protection
     *         object_id -> id of the object you are interested in. 
     *                  You can search for objId from ObjectInfo table or 
     *                  survey_object_id as it appears in other surveys
     *         object_name ->name of the object as given by users
     * @reurns: a list of objects
     */
    
    @GET
    @Path("{id}/objects")
    @Produces({"application/xml", "application/json"})
    public List<ObjectInfo> findSurveyByObjectId(
            @QueryParam("active_user") Long active_user,
            @PathParam("id") Long survey_id,
            @DefaultValue("")
            @QueryParam("survey_obj_id") String object_id,
            @DefaultValue("")
            @QueryParam("object_name")String object_name)
    {
        String q="SELECT o FROM ObjectInfo o WHERE o.surveyId.surveyId ="+survey_id;
        
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

    @java.lang.Override
    protected EntityManager getEntityManager() {
        return em;
    }
    
}
