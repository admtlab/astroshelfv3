/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package service;

import admt.message.ConfluenceCommunications;
import entity.Annotation;
import entity.ObjectInfo;
import entity.PrefQT;
import entity.Survey;
import java.util.List;
import javax.ejb.Stateless;
import javax.persistence.EntityManager;
import javax.persistence.PersistenceContext;
import javax.persistence.TypedQuery;
import javax.ws.rs.*;
import javax.ws.rs.core.Response;
import utilities.GenerateException;

/**
 *
 * @author roxy
 */
@Stateless
@Path("object")
public class ObjectInfoFacadeREST extends AbstractFacade<ObjectInfo> {
    @PersistenceContext(unitName = "astroservicePU")
    private EntityManager em;

    public ObjectInfoFacadeREST() {
        super(ObjectInfo.class);
    }

    @POST
    @Override
    @Consumes({"application/xml", "application/json"})
    public void create(ObjectInfo entity) {
        super.create(entity);
    }

    @PUT
    @Override
    @Consumes({"application/xml", "application/json"})
    public void edit(ObjectInfo entity) {
        super.edit(entity);
    }

    @POST
    @Path("{id}/delete")
    public void remove(@PathParam("id") Long id) {
        super.remove(super.find(id));
    }

    @GET
    @Path("{id}")
    @Produces({"application/xml", "application/json"})
    public Response find(@PathParam("id") Long id) {
        ObjectInfo obj = super.find(id);
        if(obj == null)
            return null;
        return Response.status(Response.Status.OK).entity(obj).header("Access-Control-Allow-Origin", "*").build();
    }

    @GET
    @Override
    @Produces({"application/xml", "application/json"})
    public List<ObjectInfo> findAll() {
        return super.findAll();
    }

    @GET
    @Path("{from}/{to}")
    @Produces({"application/xml", "application/json"})
    public Response findRange(@PathParam("from") Integer from, @PathParam("to") Integer to) {
        return Response.status(Response.Status.OK).entity(super.findRange(new int[]{from, to})).header("Access-Control-Allow-Origin", "*").build();
    }

    
    //
    // START PRIMITIVE ACTIONS
    //
    
    /*
     * @desc: returns all objects that match one or more particular criterias
     * @param: survey -> the name of the survey were object is a member
     *         surveyObjName -> the name of the object as appears in the survey
     *         obj_name -> the name of the object as given by the user
     *         object_id -> the id of the object as appear in the annotation database
     * @return:
     */
    
    
    @GET
    @Path("search")
    @Produces({"application/xml", "application/json"})
    //public List<ObjectInfo> findByObjectInfo(
    public Response findByObjectInfo(
            @DefaultValue("")
            @QueryParam("survey") String survey,
            @DefaultValue("")
            @QueryParam("survey_obj_name") String surveyObjName,
            @DefaultValue("")
            @QueryParam("obj_name") String objName,
            @DefaultValue("0") 
            @QueryParam("object_id") Long object_id) 
    {
        String q ="SELECT o from ObjectInfo o";
        
        String[][] attr={{"o.surveyId.surveyName",survey},{"o.surveyObjId",surveyObjName}, {"o.name", objName}};
        Boolean isFirst=true;
        
        for(int i=0; i<attr.length; i++)
        {
            if (!attr[i][1].equals(""))
            {
                if(isFirst)
                {
                    q= q+" WHERE ";
                    isFirst =false;
                }
                else
                    q= q+ " AND ";
                
                q =q+attr[i][0]+" LIKE \"%"+attr[i][1]+"%\"";
            }
        }
        
        if (object_id!=0)
        {
            if (isFirst)
                q =q+" WHERE ";
            else
                q=q+" AND ";
            
            q=q+" o.objectId="+object_id;
        }
        
        TypedQuery<ObjectInfo> query =getEntityManager().createQuery(q, ObjectInfo.class);
      
        return Response.status(Response.Status.OK).entity(query.getResultList()).header("Access-Control-Allow-Origin", "*").build();
    }
    
    /*
     * @desc: searches for a list of object with some particular properties given as parameters
     * @param: RA / DEC -> coordinates
     *         coord_type -> type of the coordinates
     * @return: a list of objects
     */
    
    @GET
    @Path("search/coordinates")
    @Produces({"application/xml", "application/json"})
    public Response findByCoordinates(
            @DefaultValue("1000")
            @QueryParam("ra_from") Float _ra_from,
            @DefaultValue("1000")
            @QueryParam("ra_to") Float _ra_to,
            @DefaultValue("1000")
            @QueryParam("dec_from") Float _dec_from,
            @DefaultValue("1000")
            @QueryParam("dec_to") Float _dec_to,
            @DefaultValue("")
            @QueryParam("type") String coord_type,
            @DefaultValue("10")
            @QueryParam("limit") Integer limit)
    {
        String q ="SELECT o from ObjectInfo o";
        Boolean isFirst=true;
        
        if( _ra_from!=1000 && isFirst)
        {
            q =q+" WHERE o.ra >= "+_ra_from;
            isFirst =false;
        }
        
        if (_ra_to!=1000 )
        {
            if (isFirst)
            {
                q =q+" WHERE o.ra <= "+_ra_to;
                isFirst =false;
            }
            else
                q =q + " AND o.ra <= "+_ra_to;
        }
        
        if (_dec_from!=1000 )
        {
            if (isFirst)
            {
                q =q+" WHERE o.dec >="+_dec_from;
                isFirst =false;
            }
            else
                q =q + " AND o.dec >="+_dec_from;
        }
        
        if (_dec_to!=1000 )
        {
            if (isFirst)
            {
                q =q+" WHERE o.dec <="+_dec_to;
                isFirst =false;
            }
            else
                q =q + " AND o.dec <="+_dec_to;
        }
        
        if(!coord_type.equals(""))
        {
            if (isFirst)
                q =q+" WHERE o.raType="+coord_type;
            else
                q= q+" AND o.raType="+coord_type;
        }    
        
        TypedQuery<ObjectInfo> query = getEntityManager().createQuery(q, ObjectInfo.class);
        query.setMaxResults(limit); 
        
        return Response.status(Response.Status.OK).entity(query.getResultList()).header("Access-Control-Allow-Origin", "*").build();
    }
    
    /*
     * @desc: search for all objects given: Z, COLOR, MAGNITUDE,OBJECT_TYPE
     * @param: Z -> REDSHIFT
     *         COLOR
     *         MAGNITUDE
     *         OBJECT_TYPE; all optional but at least one should be used
     * @return: a list of objects
     */
    
    @GET
    @Path("search/metadata")
    @Produces({"application/xml", "application/json"})
    public Response findByMetadata(
            @DefaultValue("1000")
            @QueryParam("z") float z,
            @DefaultValue("1000")
            @QueryParam("color") float color,
            @DefaultValue("1000")
            @QueryParam("magnitude") double magnitude,
            @DefaultValue("")
            @QueryParam("object_type") String objType) 
    {
        String q ="SELECT o from ObjectInfo o";
        Boolean isFirst=true;
        
        if (z!=1000 && isFirst)
        {
            q=q+" WHERE o.z="+z;
            isFirst=false;
        }    
        
        if(color!=1000)
        {
            if (isFirst)
            {
                q =q+" WHERE ";
                isFirst=false;
            }
            else
                q=q+" AND ";
            
            q=q+" o.color="+color;
        }
        
        if(magnitude!=1000)
        {
            if (isFirst)
            {
                q =q+" WHERE ";
                isFirst=false;
            }
            else
                q=q+" AND ";
            
            q=q+" o.magnitude="+magnitude;
        }
        
        if(!objType.equals(""))
        {
            if (isFirst)
                q =q+" WHERE ";
          
            else
                q=q+" AND ";
            
            q =q+" o.objType LIKE \"%"+objType+"%\"";
        }
        
        TypedQuery<ObjectInfo> query =getEntityManager().createQuery(q, ObjectInfo.class);
      
        return Response.status(Response.Status.OK).entity(query.getResultList()).header("Access-Control-Allow-Origin", "*").build();
    }
    
    
    @GET
    @Path("count")
    @Produces("text/plain")
    public Response countREST() {
        return Response.status(Response.Status.OK).entity(String.valueOf(super.count())).header("Access-Control-Allow-Origin", "*").build();
        
    }
    
    
    
    //
    // START ASPECTS
    //
    
    /*
     * @desc: get all anotations that are linked with one particular object
     * @param: active_user -> required for privacy protection purposes
     *         object_id -> required; id of the object used to search for annotations
     *         keyword -> optional; the keyword annotation value should contain
     *         type -> optional; type of the anotation
     * @return: a list of annotations linked to one particular object
     */
    @GET
    @Path("{id}/annotations")
    @Produces({"application/xml", "application/json"})
    public List<Annotation> findAnnotByObjectId(
            @PathParam("id") String object_id,
            @DefaultValue("0")
            @QueryParam("active_user") Long active_user,
            @DefaultValue("")
            @QueryParam("keyword") String keyword,
            @DefaultValue("")
            @QueryParam("type") String anno_type,
            @DefaultValue("false")
            @QueryParam("usePref")Boolean usePreferences
            ) throws GenerateException
    {
        /*
         * FIXME: this needs privacy protection using active_user
         */
        
        if (usePreferences && active_user==0)
        {
            throw new GenerateException("ACTIVE_USER id is required");
        }
        
        
        String q = "SELECT a FROM ObjectInfo o JOIN o.annotationCollection a WHERE o.objectId="+object_id;
        
        if(!keyword.equals(""))
            q =q+" AND a.annoValue LIKE \"%"+keyword+"%\"";
        if (!anno_type.equals(""))
            q=q+ " AND a.annoTypeId.annoTypeName="+anno_type;
        
        
        List<PrefQT> prefList=null;
        TypedQuery<Annotation> query;
        List<Annotation> result =null;
        
        if (usePreferences==true)
        {
            prefList =findAllQTByUserId("SELECT qt FROM PrefQT qt WHERE qt.userId.userId="+active_user+" ORDER BY qt.intensity DESC");
            
            String tempQuery =q;
            for (int i=0; i<prefList.size(); i++)
            {
                tempQuery=q+" AND "+prefList.get(i).getPredicate();
                query =getEntityManager().createQuery(tempQuery, Annotation.class);
                result =query.getResultList();

                if (result.size() > 0)
                    break;
            }
        }
        else
        {
            query =getEntityManager().createQuery(q, Annotation.class);
            result =query.getResultList();
            //System.err.println(result.size());
        }
      
        return result;
    }
    
    /*
     * @desc: returns all surveys were one particular object is a member
     * @param: active_user -> required for privacy protection
     *         object_id -> id of the object you are searching in the surveys
     * @reurns: a list of surveys
     */
    @GET
    @Path("{id}/surveys")
    @Produces({"application/xml", "application/json"})
    public List<Survey> findSurveysByObjectId(
            @PathParam("id") Long object_id,
            @QueryParam("active_user") Long active_user)
    {
        
        String q="SELECT s from Survey s, ObjectInfo o WHERE o.objectId="+object_id+" AND o.surveyId.surveyId=s.surveyId";
        TypedQuery<Survey> query =getEntityManager().createQuery(q, Survey.class);
        
        return query.getResultList();
    
    }
    
    @POST
    @Path("{id}/annotate")
    @Produces({"application/json"})
    @Consumes({"application/json"})
    public Response annotateObject(
            Annotation annotation,
            @PathParam("id") Long objectId
    ) {
        ObjectInfo obj = super.find(objectId);
        if(obj == null)
            return null;
        
        obj.getAnnotationCollection().add(annotation);
        getEntityManager().persist(annotation);
        annotation.getObjectInfoCollection().add(obj);
        
        getEntityManager().flush();
        getEntityManager().refresh(obj);
        getEntityManager().refresh(annotation);
        
        ConfluenceCommunications cm = new ConfluenceCommunications();
        cm.sendNewAnnotation(annotation);
        
        return Response.status(Response.Status.OK).entity(annotation).header("Access-Control-Allow-Origin", "*").build();
    }
     

    @java.lang.Override
    protected EntityManager getEntityManager() {
        return em;
    }
    
    public List<PrefQT> findAllQTByUserId(String q) {
        
        javax.persistence.TypedQuery<PrefQT> query =getEntityManager().createQuery(q, PrefQT.class);
        List<PrefQT> result=query.getResultList();
        
        return result;
    }
}
